<?php
require_once("lib/lib.php");

// list of supported Sangoma telephony cards
$telephony_cards = array(
/*
	"AFT-A500D" => array("type"=>"BRI", "device_type"=>"WAN_AFT_ISDN_BRI", "echo_cancelling"=>true, "image"=>"images/pci_a500.jpg", "dtmf_detection"=>true),
	"AFT-A500" => array("type"=>"BRI", "device_type"=>"WAN_AFT_ISDN_BRI", "image"=>"images/pci_a500.jpg"),
	"AFT-B700" => array("type"=>"BRI", "device_type"=>"WAN_AFT_ISDN_BRI", "image"=>"images/flex_bri_2.jpg", "fax_detection"=>true, "dtmf_detection"=>true, "echo_cancelling"=>true),
*/

	"AFT-A101D" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "echo_cancelling"=>true, "image"=>"images/small_a101.jpg", "S514CPU"=>"function_get_S514CPU:PORT"),
	"AFT-A102D" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "echo_cancelling"=>true, "image"=>"images/small_a102.jpg", "S514CPU"=>"function_get_S514CPU:PORT"),
	"AFT-A104D" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "echo_cancelling"=>true, "image"=>"images/small_a104.jpg", "S514CPU"=>"A"),
	"AFT-A108D" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "echo_cancelling"=>true, "image"=>"images/small_a108.jpg", "S514CPU"=>"A"),

	"AFT-A101" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "S514CPU"=>"function_get_S514CPU:PORT", "image"=>"images/small_a101.jpg"),
	"AFT-A102" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "S514CPU"=>"function_get_S514CPU:PORT", "image"=>"images/small_a102.jpg"),
	"AFT-A104" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "S514CPU"=>"A", "image"=>"images/small_a104.jpg"),
	"AFT-A108" => array("type"=>"PRI", "device_type"=>"WAN_AFT_TE1", "S514CPU"=>"A", "image"=>"images/small_a108.jpg"),
);

function verify_wanrouter_output($out)
{
	if(substr_count(strtolower($out), "command not found") || substr_count(strtolower($out),"no such device"))
		return false;
	return true;
}

function get_spans($out = null)
{
	global $telephony_cards;

	if(!$out)
		$out = shell_command("server_hwprobe");
	if(!verify_wanrouter_output($out)) {
		errormess("No wanpipe cards present:".$out, "no");
		return;
	}

	$out = explode("\n",$out);

/*
// $out will be something like this
Array ( 
	[0] => 
	[1] => ----------------------------------------- 
	[2] => | Wanpipe Hardware Probe Info (verbose) | 
	[3] => ----------------------------------------- 
	[4] => 1 . AFT-B700-SH : SLOT=4 : BUS=4 : IRQ=11 : PORT=1 : HWEC=16 : V=34 
	[5] => +01:TE: PCIe: PLX2: C01 
	[6] => 2 . AFT-B700-SH : SLOT=4 : BUS=4 : IRQ=11 : PORT=2 : HWEC=16 : V=34 
	[7] => +02:TE: PCI: NONE: C01 
	[8] => 3 . AFT-B700-SH : SLOT=4 : BUS=4 : IRQ=11 : PORT=3 : HWEC=16 : V=34 
	[9] => +03:NT: PCI: NONE: C01 
	[10] => 4 . AFT-B700-SH : SLOT=4 : BUS=4 : IRQ=11 : PORT=4 : HWEC=16 : V=34 
	[11] => +04:NT: PCI: NONE: C01 
	[12] => 
	[13] => Card Cnt: B700=1 
	[14] => 
) 

or

Array ( 
	[0] => 
	[1] => ----------------------------------------- 
	[2] => | Wanpipe Hardware Probe Info (verbose) | 
	[3] => ----------------------------------------- 
	[4] => 1 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=1 : V=13 
	[5] => +01:PMC4354: PCI 
	[6] => 2 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=2 : V=13 
	[7] => +02:PMC4354: PCI 
	[8] => 3 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=3 : V=13 
	[9] => +03:PMC4354: PCI 
	[10] => 4 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=4 : V=13 
	[11] => +04:PMC4354: PCI 
	[12] => 
	[13] => Card Cnt: A104=1 
	[14] => 
)

*/

// for testing purposes
/*$out=Array (
	"\n" ,
	"-----------------------------------------",
	"| Wanpipe Hardware Probe Info (verbose) |" ,
	"----------------------------------------- ",
	"1 . AFT-A102D : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=1 : V=13 ",
	"01:PMC4354: PCI ",
	"2 . AFT-A102D : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=2 : V=13 ",
	"+02:PMC4354: PCI ",
	"3 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=1 : V=13 ",
	"+03:PMC4354: PCI ",
	"4 . AFT-A104u : SLOT=9 : BUS=0 : IRQ=16 : CPU=A : PORT=2 : V=13 ",
	"+04:PMC4354: PCI ",
	"5. AFT-B700-SH : SLOT=4 : BUS=4 : IRQ=11 : PORT=1 : HWEC=16 : V=34 ",
	"+01:TE: PCIe: PLX2: C01 ",
	"6. AFT-A500D : SLOT=4 : BUS=4 : IRQ=11 : PORT=2 : HWEC=16 : V=34 ",
	"+02:TE: PCI: NONE: C01",
	"\n",
	"Card Cnt: A104=1 ",
	"\n"
);*/

	$ports = (count($out)-4-3) / 2;      // 4 useless lines at the beginning and 3 at the end, per 2 because there are 2 lines for every port

	$cards = array();
	for($i=4; $i<count($out)-4; $i=$i+2) {
		$line = explode(":",$out[$i]);
		$card = trim($line[0]);
		$card = explode(".", $card);
		$card = array("card"=>trim($card[1]));
		$tel_card = get_telephony_card($card["card"]);
		// ingnore unknown cards
		if(!$tel_card)
			continue;
		$card["telephony_card"] = $tel_card;
		for($j=1; $j<count($line); $j++) {
			$setting = explode("=", $line[$j]);
			$card[trim($setting[0])] = trim($setting[1]);
		}
		$line = explode(":",$out[$i+1]);
		$card["type"] = trim($line[1]);
		array_push($cards, $card);
	}
/*
// $cards will look like this
Array (
	[0] => Array ( [card] => AFT-B700-SH [SLOT] => 4 [BUS] => 4 [IRQ] => 11 [PORT] => 1 [HWEC] => 16 [V] => 34 [type] => TE ) 
	[1] => Array ( [card] => AFT-B700-SH [SLOT] => 4 [BUS] => 4 [IRQ] => 11 [PORT] => 2 [HWEC] => 16 [V] => 34 [type] => TE ) 
	[2] => Array ( [card] => AFT-B700-SH [SLOT] => 4 [BUS] => 4 [IRQ] => 11 [PORT] => 3 [HWEC] => 16 [V] => 34 [type] => NT ) 
	[3] => Array ( [card] => AFT-B700-SH [SLOT] => 4 [BUS] => 4 [IRQ] => 11 [PORT] => 4 [HWEC] => 16 [V] => 34 [type] => NT ) 
)
// if there are more cards then they will appear here from index 4 ... (depending on the number of ports each card has) 
*/
	return $cards;
}

function get_cards($spans)
{
	$physical_cards = array();
	$slot = $cards[0]["SLOT"];
	$bus = $cards[0]["BUS"];
	$cards = array();
	for($i=1; $i<count($spans); $i++)
	{
		$index = $i;
		$card = array();
		while($slot==$spans[$i]["SLOT"] && $bus==$spans[$i]["BUS"] && $i<count($spans)) {
			array_push($card, $spans[$i]);
			$i++;
		}
		$cards[$card[0]["card"]] = $card;
	}
	return $cards;
}

function get_span_steps($spans)
{
	global $telephony_cards;

	$dial_plan = array("from-pstn", "custom");
	$dial_plan["selected"] = "from-pstn";

	$bri_types = array("TE (Point to point)", "NT (Point to Multipoint)");
	$bri_types["selected"] = "TE (Point to point)";
	$pri_types = array("E1", "T1");
	$pri_types["selected"] = "E1";

	$fields_bri = array(
					"step_image" => "",
					"step_description" => _("Each of the ports of the cards in your system has to be configured separately.<br/> Depending on how the card is physically set the connection type is either 'Point to point' or 'Point to multipoint'."),
					"step_name" => _("Configuring "),
					"hardware_dtmf_detection" => array("column_name"=>_("Hardware dtmf detection"), "display"=>"checkbox"),
					"hardware_fax_detection" => array("column_name"=>_("Hardware fax detection"), "display"=>"checkbox"),
					"hardware_echo_canceller" => array("value"=>"f", "display"=>"checkbox"),
					"connection_type" => array($bri_types, "display"=>"select", "column_name"=>"Connection type", "comment"=>_("If you wish to configure this port in a different way please change the position of the card.")), // this can be NT or TE (NT-> Point to Multipoint, TE-> Point to point) 
					"group" => array("value"=>1, "column_name"=>_("Span"), "display"=>"fixed", "compulsory"=>true),
				//	"dial_plan" => array($dial_plan, "column_name"=>_("Dial plan"), "display"=>"select", "compulsory"=>true, "javascript"=>'onChange="show_dial_plan_context();"'),
				//	"dial_plan_context" => array("column_name"=>_("Dial plan context"), "comment"=>_("Numeric"), "triggered_by"=>"--"),
				//	"TEI" => array("value"=>127, "column_name"=>"TEI", "comment"=>_("Terminal equipment identifier. 127	 when Connection type is 'Point to multipoint', another number between 1-126 otherwise")),
				);

/*
	if($card_port->type == "E1") {
		$FE_LCODE = "HDB3";
		$FE_FRAME = "CRC4";
	}else{
		$FE_LCODE = "B8ZS";
		$FE_FRAME = "ESF";
	}
*/

	$fe_lcode = array("HDB3", "AMI");
	$fe_lcode["selected"] = "HDB3";

	$fe_frame = array("CRC4", "NCRC4");
	$fe_frame["selected"] = "CRC4";

	$fields_pri = array(
					"step_image" => "",
					"step_description" => _(""),
					"step_name" => _("Configuring "),
					"on_submit" => "set_voice_channels",
					"connection_type" => array($pri_types, "display"=>"select", "compulsory"=>true, "javascript"=>'onChange="set_timeslots();"'),
					"hardware_echo_canceller" => array("value"=>"f", "display"=>"checkbox"),
					"front_end_line_coding"=>array($fe_lcode, "display"=>"select", "compulsory"=>true),
					"front_end_framing"=>array($fe_frame, "display"=>"select", "compulsory"=>true),
					"message" => array("display"=>"message", "value"=>"SIG interface"),
					"sig_interface" => array("value"=>"w_port_g1", "display"=>"fixed"),
					"sig_channels" => array("value"=>"16", "column_name"=>"active channels", "display"=>"text", "comment"=>"Hardware Timeslot Number or interval.", "compulsory"=>true, "javascript"=>' onblur="set_voice_channels();"'),
					"message2" => array("display"=>"message", "value"=>"Voice interface"),
					"voice_interface" => array("value"=>"w_port_g2", "display"=>"fixed"),
					"voice_channels" => array("value"=>"1-15.17-31", "column_name"=>"active channels", "display"=>"text-nonedit", "comment"=>"Hardware Timeslot Number or interval", "compulsory"=>true)
				);

	$spans = get_spans();
	$steps = array();
	for($i=0; $i<count($spans); $i++) 
	{
		$interface_nr = $i+1;
		$telephony_card = $telephony_cards[$spans[$i]["telephony_card"]];
		$card_type = strtolower($telephony_card["type"]);
		$flds = ${"fields_".$card_type};
		$flds["step_name"] = _("Configuring ").strtoupper($card_type).": ".$spans[$i]["card"]./*" SLOT ".$spans[$i]["SLOT"]." BUS ".$spans[$i]["BUS"].*/" PORT ".$spans[$i]["PORT"];
		$flds["step_image"] = (isset($telephony_card["image"])) ? $telephony_card["image"] : "";
		if($card_type == "bri") {
			if($spans[$i]["type"] == "NT")
				$flds["connection_type"]["value"] = "Point to multipoint";
			$flds["group"]["value"] = $i+1;  // this line forces having the 1-1 relation between span and port

			if(!isset($telephony_card["dtmf_detection"]))
				unset($flds["hardware_dtmf_detection"]);
			if(!isset($telephony_card["fax_detection"]))
				unset($flds["hardware_fax_detection"]);
			if(!isset($telephony_card["echo_cancelling"]) || $telephony_card["echo_cancelling"]!==true)
				unset($flds["hardware_echo_canceller"]);

		}elseif($card_type == "pri") {
			$flds["sig_interface"]["value"] = "w".$interface_nr."g1";
			$flds["voice_interface"]["value"] = "w".$interface_nr."g2";
			if(!isset($telephony_card["echo_cancelling"]) || $telephony_card["echo_cancelling"]!==true)
				unset($flds["hardware_echo_canceller"]);
		}
		array_push($steps, $flds);
	}
	return $steps; 
}

function get_telephony_card($card)
{
	global $telephony_cards;

	foreach($telephony_cards as $name=>$details)
	{
		if(substr($card,0,strlen($name)) == $name)
			return $name;
	}
	return false;
}

function get_port_type($type)
{
	if($type == "TE" || $type == "TE (Point to point)")
		return "TE (Point to point)";
	elseif($type == "NT" || $type == "NT (Point to multipoint)")
		return "NT (Point to multipoint)";
	else
		return $type; // E1/T1
}

// this should be called only for A101 and A102
function get_S514CPU($port)
{
	if($port == 1)
		return "A";
	elseif($port == 2)
		return "B";
	return "A";
}

function configure_PRI_file($span, $nr, $fields=array())
{
	global $telephony_cards;

	$tel_card = $telephony_cards[$span["telephony_card"]];
	$device_type = $tel_card["device_type"];

	$card_port = new Card_port;
	$card_port->SLOT = $span["SLOT"];
	$card_port->BUS = $span["BUS"];
	$card_port->PORT = $span["PORT"];
	$card_port->filename = "wanpipe$nr.conf";
	$card_port->type = $fields["connection_type"];
	$card_port->card_type = "PRI";
	$card_port->voice_interface = "w".$nr."g2";
	$card_port->sig_interface = "w".$nr."g1";
	$card_port->name = "$nr. ".$span["telephony_card"]."(PRI-".$card_port->type.")"." PORT ". $span["PORT"]; // this will be unique
	$card_port->voice_chans = $fields["voice_channels"];
	$card_port->sig_chans = $fields["sig_channels"];
	$card_port->echocancel = (isset($fields["hardware_echo_canceller"]) && $fields["hardware_echo_canceller"] == "on") ? "t" : "f";
	$card_port->dtmfdetect = (isset($fields["hardware_dtmf_detection"]) && $fields["hardware_dtmf_detection"] == "on") ? "t" : "f";

	$res = $card_port->insert();
	if(!$res[0]) 
		return array(false, _("Could not insert card port in the database:".$res[1]));

/*	$hw_dtmf = ($fields["hardware_dtmf_detection"] == "on") ? "YES" : "NO";
	$hw_fax = ($fields["hardware_fax_detection"] == "on") ? "YES" : "NO";*/

	if($card_port->type == "E1") {
		$FE_LCODE = ($fields["front_end_line_coding"]) ? $fields["front_end_line_coding"] : "HDB3";
		$FE_FRAME = ($fields["front_end_framing"]) ? $fields["front_end_framing"] : "CRC4";
	}else{
		$FE_LCODE = ($fields["front_end_line_coding"]) ? $fields["front_end_line_coding"] : "B8ZS";
		$FE_FRAME = ($fields["front_end_framing"]) ? $fields["front_end_framing"] : "ESF";
	}

	$hw_echo_canceller = (isset($fields["hardware_echo_canceller"]) && $fields["hardware_echo_canceller"] == "on") ? "YES" : "NO";
	$hw_dtmf_detect = (isset($fields["hardware_dtmf_detection"]) && $fields["hardware_dtmf_detection"] == "on") ? "YES" : "NO";

	$s514cpu = $tel_card["S514CPU"]; 
	if(substr($s514cpu,0,9) == "function_") {
		$func = substr($s514cpu,9,strlen($s514cpu));
		$expld = explode(":",$func);
		$func = $expld[0];
		$param = (isset($span[$expld[1]])) ? $span[$expld[1]] : $fields[$expld[1]];
		$s514cpu = $func($param);
	}

	$file = 
"#================================================
# WANPIPE$nr Configuration File
#================================================
#
# Note: This file was generated automatically
#       by Freesentral web interface
#
#       If you want to edit this file, it is
#       recommended that you use the web interface
#       to do so.
#================================================

[devices]
wanpipe$nr = $device_type, Comment

[interfaces]
w".$nr."g1 = wanpipe$nr, , API, Comment
w".$nr."g2 = wanpipe$nr, , API, Comment

[wanpipe$nr]
CARD_TYPE       = AFT
S514CPU         = $s514cpu
CommPort        = PRI
AUTO_PCISLOT    = YES
FE_MEDIA        = ".$card_port->type."
FE_LCODE        = $FE_LCODE
FE_FRAME        = $FE_FRAME
FE_LINE         = ".$span["PORT"]."
TE_CLOCK        = NORMAL
TE_REF_CLOCK    = 0
TE_HIGHIMPEDANCE        = NO
TE_RX_SLEVEL    = 120
LBO             = 120OH
TE_SIG_MODE     = CCS
FE_TXTRISTATE   = NO
MTU             = 1500
UDPPORT         = 9000
TTL             = 255
IGNORE_FRONT_END = NO
TDMV_HW_DTMF    = $hw_dtmf_detect
";
	if($fields["sig_channels"])
		$file .=
"

[w".$nr."g1]
HDLC_STREAMING  = YES
ACTIVE_CH       = ".$fields["sig_channels"]."
MTU             = 0
MRU             = 0
DATA_MUX        = NO
TDMV_HWEC       = $hw_echo_canceller
";

$file .=
"
[w".$nr."g2]
HDLC_STREAMING  = NO
ACTIVE_CH       = ".$fields["voice_channels"]."
IDLE_FLAG       = 0x7E
MTU             = 0
MRU             = 0
DATA_MUX        = NO
TDMV_HWEC       = $hw_echo_canceller
";

//	print(str_replace("\n", "<br/>", $file));
//	exit();
	$filename = "/etc/wanpipe/wanpipe$nr.conf";
	if(is_file($filename)) {
		$boolres = rename($filename, str_replace(".conf", ".old", $filename));
		if(!$boolres)
			return array(false, _("Could not save old file")." $filename. "."<br/>");
	}

	$fh = fopen($filename, "w");
	$res = fwrite($fh, $file);
	fclose($fh);
	if(!$res)
		return array(false, _("Could not write to")." $filename. "."<br/>");
	return array(true, "");
}

function configure_BRI_file($span, $nr, $fields=array())
{
	global $telephony_cards;

	$tel_card = $telephony_cards[$span["telephony_card"]];
	$device_type = $tel_card["device_type"];

	$card_port = new Card_port;
	$card_port->SLOT = $span["SLOT"];
	$card_port->BUS = $span["BUS"];
	$card_port->PORT = $span["PORT"];
	$card_port->filename = "wanpipe$nr.conf";
	$card_port->span = $fields["group"];
	$card_port->type = $span["type"];
	$card_port->card_type = "BRI";
	$card_port->voice_interface = "";
	$card_port->name = "$nr. ".$span["telephony_card"]."(BRI)"." PORT ". $span["PORT"];	// this will be unique
//	$card_port->voice_chans = $fields["voice_channels"];
//	$card_port->sig_chans = $fields["sig_channels"];
	$card_port->echocancel = (isset($fields["hardware_echo_canceller"]) && $fields["hardware_echo_canceller"] == "on") ? "t" : "f";
	$card_port->dtmfdetect = (isset($fields["hardware_dtmf_detection"]) && $fields["hardware_dtmf_detection"] == "on") ? "t" : "f";

	$res = $card_port->insert();
	if(!$res[0]) 
		return array(false, _("Could not insert BRI port in the database."));

	$hw_dtmf = (isset($fields["hardware_dtmf_detection"]) && $fields["hardware_dtmf_detection"] == "on") ? "YES" : "NO";
	$hw_fax = (isset($fields["hardware_fax_detection"]) && $fields["hardware_fax_detection"] == "on") ? "YES" : "NO";

	$hw_echo_canceller = (isset($fields["hardware_echo_canceller"]) && $fields["hardware_echo_canceller"]=="on") ? "YES" : "NO";

	$file = 
"#================================================
# WANPIPE$nr Configuration File
#================================================
#
# Note: This file was generated automatically
#       by Freesentral web interface
#
#       If you want to edit this file, it is
#       recommended that you use the web interface
#       to do so.
#================================================

[devices]
wanpipe$nr = $device_type, Comment

[interfaces]
w$nr"."g".$fields["group"]." = wanpipe$nr, , TDM_VOICE_API, Comment

[wanpipe$nr]
CARD_TYPE 	= AFT
S514CPU 	= A
CommPort 	= PRI
AUTO_PCISLOT 	= NO
PCISLOT 	= ".$span["SLOT"]."
PCIBUS  	= ".$span["BUS"]."
FE_MEDIA	= BRI
FE_LINE		= $nr
TDMV_LAW	= ALAW
TDMV_DUMMY_REF	= NO
MTU 		= 1500
UDPPORT 	= 9000
TTL		= 255
IGNORE_FRONT_END = NO
TDMV_SPAN	= ".$fields["group"]."
TDMV_HW_DTMF	= $hw_dtmf
TDMV_HW_FAX_DETECT = $hw_fax

[w$nr"."g".$fields["group"]."]
ACTIVE_CH	= ALL
TDMV_HWEC	= $hw_echo_canceller
MTU 		= 80
";
//	print(str_replace("\n", "<br/>", $file));
//	exit();
	$filename = "/etc/wanpipe/wanpipe$nr.conf";
	if(is_file($filename)) {
		$boolres = rename($filename, str_replace(".conf", ".old", $filename));
		if(!$boolres)
			return array(false, _("Could not save old file")." $filename. "."<br/>");
	}

	$fh = fopen($filename, "w");
	$res = fwrite($fh, $file);
	fclose($fh);
	if(!$res)
		return array(false, _("Could not write to")." $filename. "."<br/>");
	return array(true, "");
}

/*
[devices]
wanpipe1 = WAN_AFT_TE1, Comment

[interfaces]
w1g1 = wanpipe1, , API, Comment
w1g2 = wanpipe1, , API, Comment

[wanpipe1]
CARD_TYPE       = AFT
S514CPU         = A
CommPort        = PRI
AUTO_PCISLOT    = NO
PCISLOT         = 12
PCIBUS          = 0
FE_MEDIA        = E1
FE_LCODE        = HDB3
FE_FRAME        = CRC4
FE_LINE         = 1
TE_CLOCK        = NORMAL
TE_REF_CLOCK    = 0
TE_HIGHIMPEDANCE        = NO
LBO             = 120OH
TE_SIG_MODE     = CCS
FE_TXTRISTATE   = NO
MTU             = 1500
UDPPORT         = 9000
TTL             = 255
IGNORE_FRONT_END = NO

[w1g1]
HDLC_STREAMING  = YES
ACTIVE_CH       = 16
MTU             = 1500
MRU             = 1500
DATA_MUX        = NO
TDMV_HWEC       = NO

[w1g2]
HDLC_STREAMING  = NO
ACTIVE_CH       = 1-15.17-31
IDLE_FLAG       = 0x7E
MTU             = 1500
MRU             = 1500
DATA_MUX        = NO
TDMV_HWEC       = NO

*/
?>