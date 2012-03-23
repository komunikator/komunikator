<?php
/**
 * call_forward.php
 * This file is part of the FreeSentral Project http://freesentral.com
 *
 * FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
 * Copyright (C) 2008-2009 Null Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */
?>
<div class="content wide">
<?php
global $module, $method, $action;

$extension = $_SESSION["user"];

if(!$method)
	$method = $module;

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$call();

function call_forward_database()
{
	$extension = new Extension;
	$extension->extension = $_SESSION['user'];
	$extension->select('extension');
	$forward = getparam("forward");
	if($forward == "yes")
	{
		$forward_number = getparam("forward_number");
		if(!$forward || !$forward_number) {
			errormess("You've enabled forward but did not inserted the number");
			return;
		}
		$forward = $forward_number;
	} else if ($forward != "vm")
	$forward = '';

	$forward_busy = getparam("forward_busy");
	if($forward_busy == "yes") {
		$forward_busy_number = getparam("forward_busy_number");
		if(!$forward_busy_number || $forward_busy_number == '') {
			errormess("You've enabled forward busy but did not inserted the number");
			return;
		}
		$forward_busy = $forward_busy_number;
	}elseif($forward_busy != "vm")
		$forward_busy = '';
	$forward_noanswer = getparam("forward_NoAnswer");
	if($forward_noanswer == "yes") {
		$forward_noanswer_number = getparam("forward_NoAnswer_number");
		if(!$forward_noanswer_number || $forward_noanswer_number == '') {
			errormess("You've enabled forward noanswer but did not inserted the number");
			return;
		}
		$forward_noanswer = $forward_noanswer_number;
    }elseif($forward_noanswer != "vm")
		$forward_noanswer = '';

	$timeout = getparam("noAnswer_timeout");
	if(!$timeout)
		$timeout = 60;

	$old_timeout = getparam("oldtimeout");

	$res = makeupdate("forward",$forward,$old_timeout,$extension->extension_id);
	$res .= makeupdate("forward_busy",$forward_busy,$old_timeout,$extension->extension_id);
	$res .= makeupdate("forward_noanswer",$forward_noanswer,$old_timeout,$extension->extension_id);
	$res .= makeupdate("noanswer_timeout",$timeout,$old_timeout,$extension->extension_id);

	if(!$res)
		//message("Succesfully updated settings");
		notice("Succesfully updated settings");
	else
		//errormess($res);
		notice($res, NULL, false);
}

function addOneCondition($name)
{
	return "MAX(CASE WHEN param='$name' THEN value END) as $name";
}

function makeupdate($param,$value,$oldtimeout,$extension_id)
{
	$pbx_settings = Model::selection('pbx_setting',array("extension_id"=>$extension_id, "param"=>$param));
	if(!count($pbx_settings)) {
		$pbx_setting = new Pbx_Setting;
		$res = $pbx_setting->add(array("extension_id"=>$extension_id, "param"=>$param, "value"=>$value));
	}elseif(count($pbx_settings) == 1) {
		$pbx_setting = $pbx_settings[0];
		$res = $pbx_setting->edit(array("value"=>$value));
	}

	if (!$res[0])
    	return $res[1]."<br/>";
	else
		return;
}

function insert_forward_options($value, $name)
{
	?>
	<input type="radio" name="<?php print $name?>" value="yes" <?php if ($value != "" && $value != "vm") print ('CHECKED');?>>Yes
&nbsp;&nbsp;<input type="radio" name="<?php print $name;?>" value="no" <?php if ($value == "") print ('CHECKED');?>>No
&nbsp;&nbsp;<input type="radio" name="<?php print $name;?>" value="vm" <?php if ($value == "vm") print ('CHECKED');?>>Voicemail
	<?php
}

function call_forward()
{
	$fields = addOneCondition("forward");
	$fields .= ','.addOneCondition("forward_busy");
	$fields .= ','.addOneCondition("forward_noanswer");
	$fields .= ','.addOneCondition("noanswer_timeout");
	
	$lextension = new extension;
	$lextension->extension = $_SESSION['user'];
	$lextension->select('extension');
	if(!$lextension->extension_id) {
		//errormess("I dont't have the id for this extension");
		notice("I dont't have the id for this extension", NULL, false);
		return;
	}
	
	$pbx_setting = new Pbx_Setting;
	$res = $pbx_setting->fieldSelect($fields,array('extension_id'=>$lextension->extension_id)); 
	
	$forward = $res[0]["forward"];
	$forward_busy = $res[0]["forward_busy"];
	$forward_noanswer = $res[0]["forward_noanswer"];
	$timeout = $res[0]["noanswer_timeout"];

    if ($forward == NULL)
	$forward = "";
    if ($forward_busy == NULL)
	$forward_busy = "";
    if ($forward_noanswer == NULL)
	$forward_noanswer = "";

	$forward_number = ($forward != "vm") ? $forward : NULL;
	$forward_busy_number = ($forward_busy != "vm") ? $forward_busy : NULL;
	$forward_noanswer_number = ($forward_noanswer != "vm") ? $forward_noanswer : NULL;

	$fields = array(
					"forward"=>array("value"=>$forward,"display"=>"insert_forward_options"),
					"forward_number"=>array("value"=>$forward_number),
					"forward_busy"=>array("value"=>$forward_busy,"display"=>"insert_forward_options"),
					"forward_busy_number"=>array("value"=>$forward_busy_number),
					"forward_NoAnswer"=>array("value"=>$forward_noanswer,"display"=>"insert_forward_options"),
					"forward_NoAnswer_number"=>array("value"=>$forward_noanswer_number),
					"noAnswer_timeout"=>array("value"=>$timeout)
				);
	start_form();
	addHidden("database");
	editObject(NULL,$fields,"Call Forward Settings", "Save");
	end_form();
}
?>
</div>