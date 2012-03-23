<?php
require_once("framework.php");
require_once("socketconn.php");

class Sig_trunk extends Model
{
	public static function variables()
	{
		return array(
					"sig_trunk_id" => new Variable("serial"),
					"sig_trunk" => new Variable("text", "!null"),
					"enable" => new Variable("text", "no"),
					"type" => new Variable("text", "isdn-pri-cpe"),	// isdn-bri-net, isdn-bri-cpe, isdn-pri-net, isdn-pri-cpe, ss7-isup
					"switchtype" => new Variable("text", "!null"),	// euro-isdn-e1, euro-isdn-t1, national-isdn, dms100, lucent5e, att4ess, qsig, unknown
					"sig" => new Variable("text"),	// should point to conf entry from card_conf where param_name=section name
					"voice" => new Variable("text"),	// should point to conf entry from card_conf where param_name=section name
					"number" => new Variable("text"),
					"rxunderrun" => new Variable("int2","0"),
					"strategy" => new Variable("text", "increment"),	// increment, decrement, lowest, highest, random
					"strategy-restrict" => new Variable("text"),	// even, odd, even-fallback, odd-fallback
					"userparttest" => new Variable("int2", 60),
					"channelsync" => new Variable("int4", 1000),
					"channellock" => new Variable("int4", 10000),
					"numplan" => new Variable("text", "unknown"),	// unknown, isdn, data, telex, national, private
					"numtype" => new Variable("text", "unknown"),	// unknown,international,national,net-specific,subscriber,abbreviated,reserved
					"presentation" => new Variable("text", "allowed"),	// allowed, restricted, unavailable
					"screening" => new Variable("text", "user-provided"),	// user-provided, user-provided-passed, user-provided-failed, network-provided
					"format" => new Variable("text"),
					"print-messages" => new Variable("text", "no"),
					"print-frames" => new Variable("text", "no"),
					"extended-debug" => new Variable("text", "no"),
					"layer2dump" => new Variable("text"),
					"layer3dump" => new Variable("text"),

					"port" => new Variable("text", "!null", "card_ports", false, "name")	// from here sig and voice will be taken
				);
	}

	public function setObj($params)
	{
		if(field_value("sig_trunk", $params)) {
			$this->sig_trunk = field_value("sig_trunk",$params);
			if($this->objectExists())
				return array(false, "This gateway was already defined.");
		}
		if($this->sig_trunk_id)
			$this->select();
		return parent::setObj($params);
	}

	// possible operations: configure/create
	// to delete set: enable=no
	public function sendCommand($operation)
	{
		$socket = new SocketConn;
		if($socket->error == "") {
			if($operation == "remove") {
				$this->enable = "no";
				$operation = "configure";
			}
			$str = "control sig $operation section=".$this->sig_trunk.' '. $this->toString('', array("sig_trunk", "sig_trunk_id"));
			$card_confs = Model::selection("card_conf", array("section_name"=>$this->sig));
			for($i=0; $i<count($card_confs); $i++) {
				$str .= " ".$this->sig.'.'.$card_confs[$i]->param_name.'='.$card_confs[$i]->param_value;
			}
			if(count($card_confs))
				$str .= " ".$this->sig.'.'."module".'='.$card_confs[0]->module_name;
			$socket->write($str);
		}
	}
}

?>