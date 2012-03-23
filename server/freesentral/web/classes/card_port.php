<?php
require_once("framework.php");

// this objects will match the hardware on the system
class Card_port extends Model
{
	public static function variables()
	{
		return array(
					"BUS" => new Variable("int2", "!null"),  // BUS, SLOT, PORT will uniquely identify this object
					"SLOT" => new Variable("int2", "!null"), //
					"PORT" => new Variable("int2", "!null"), //
					"filename" => new Variable("text", "!null"),  // "wanpipe1.conf" ... "wanpipe2.conf"
					"span" => new Variable("text"),   // numeric 
					"type" => new Variable("text", "!null"),	// E1/T1  or NT/TE
					"card_type" => new Variable("text"),	// BRI or PRI
					"voice_interface" => new Variable("text"),
					"sig_interface" => new Variable("text"),
					"voice_chans" => new Variable("text"),
					"sig_chans" => new Variable("text"),
					"echocancel" => new Variable("bool", "f"),
					"dtmfdetect" => new Variable("bool", "f"),

					"name" => new Variable("text", "!null")	// hold the name of the card and the port number 
				);
	}

	public function setObj($fields)
	{
		$this->BUS = field_value("BUS", $fields);
		$this->SLOT = field_value("SLOT", $fields);
		$this->PORT = field_value("PORT", $fields);
		if($this->objectExists())
			return array(false, "This port is already configured");
		return parent::setObj($fields);
	}
}

?>