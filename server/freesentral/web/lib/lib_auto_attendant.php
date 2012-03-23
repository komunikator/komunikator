<?php
/**
 * lib_auto_attendant.php
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
<?php
global $module, $method, $path, $action, $page, $target_path, $iframe;

if($_SESSION["level"] != "admin")
{
	forbidden();
}

function set_step($nr,$message,$img,$brs=2)
{
	print '<div class="notify">';
	print "Step ".$nr." :: ".$message;
	if($img == "complete")
		print '<img src="images/complete.gif" alt="complete" title="Complete" width="35px"/>';
	else
		print '<img src="images/incomplete.gif" alt="incomplete" title="Incomplete" width="20px" />';
	for($i=0;$i<$brs;$i++)
		print "<br/>";
	print "</div>";
}

function DID()
{
	activate();
}

function activate($error = NULL)
{
	global $method;

	$did = new Did;
	$did->extend(array("extension"=>"extensions", "group"=>"groups"));
	$dids = $did->extendedSelect(array("destination"=>"external/nodata/auto_attendant.php"),"number");
	if (count($dids))
	{
		if ($method == "auto_attendant" || $method == "wizard")
			set_step(4,"DIDs for Auto Attendant","complete");
		$method = "activate";
		$formats = array("DID"=>"number", "function_get_default_destination:default_destination"=>"extension,group");
		$actions =  array("&method=activate_did"=>'<img src="images/edit.gif" title="Edit" alt="edit"/>', "&method=delete_did"=>'<img src="images/delete.gif" title="Delete" alt="delete"/>');
		tableOfObjects($dids, $formats, "did", $actions, array("&method=activate_did"=>"Add DID for AutoAttendant"));
	} else {
		if ($method == "auto_attendant" || $method == "wizard")
			set_step(4,"Activate did for Auto Attendant","incomplete");
		$method = "activate";
		activate_did();
	}
}

function delete_did()
{
	ack_delete("did", getparam("did"), NULL, "did_id", getparam("did_id"));
}

function delete_did_database()
{
	global $module;

	$did = new Did;
	$did->did_id  = getparam("did_id");
	$res = $did->objDelete();
	unset($_POST["did_id"]);
	unset($_GET["did_id"]);
	notice($res[1], $module, $res[0]);
}

function activate_did($error=NULL)
{
	if($error)
		errornote($error);

	$did = new Did;
	$did->did_id = getparam("did_id");
	$did->select();

	if($error) {
		$did->number = getparam("number");
		$did->destination = getparam("destination");
		$did->default_destination = getparam("default_destination");
	}

	$def = build_default_options($did);

	$fields = array(
		"number"=>array("compulsory"=>true, "column_name"=>"DID", "comment"=>"Incoming phone number."),
		"default_destination" => array($def, "display"=>"select", "comment"=>"Choose a group or an extension for the call to go to if no digit was pressed in Auto Attendant.", "compulsory"=>true),
	);

	$title = "DID for Auto Attendant";
	start_form();
	addHidden("database",array("method"=>"activate_did", "did_id"=>$did->did_id));
	editObject($did,$fields,$title,"Save",true);
	end_form();
}

function build_default_options($did)
{
	$extensions = Model::selection("extension", NULL, "extension");
	$extensions = Model::objectsToArray($extensions, array("extension_id" => "function_build_extension_id:default_destination_id", "extension"=>"default_destination"),true);
	$groups = Model::selection("group", NULL, '"group"');
	$groups = Model::objectsToArray($groups,array("group_id" => "function_build_group_id:default_destination_id", "group"=>"default_destination"),true);

	$def = array_merge(
			array(array("default_destination_id"=>"__disabled", "default_destination"=>"--Groups--")),
			$groups,
			array(array("default_destination_id"=>"__disabled", "default_destination"=>"--Extensions--")),
			$extensions
	);

	if ($did->extension_id)
		$def["selected"] = "extension:".$did->extension_id;
	elseif ($did->group_id)
		$def["selected"] = "group:".$did->group_id;

	return $def;
}

function build_extension_id($id)
{
	return "extension:$id";
}

function build_group_id($id)
{
	return "group:$id";
}

function activate_did_database()
{
	$did = new Did;
	$did->did_id  = getparam("did_id");
	$params = form_params(array("number","description"));
	$params["destination"] = "external/nodata/auto_attendant.php";
	$def = explode(":",getparam("default_destination"));
	if (count($def)==2) {
		$params[$def[0]."_id"] = $def[1];
		$oth = ($def[0] == "extension") ? "group" : "extension";
		$params[$oth."_id"] = null;
	} else {
		activate_did("You must select a 'Default destination'.");
		return;
	}

	$res = ($did->did_id) ? $did->edit($params) : $did->add($params);

	if($res[0])
		notice($res[1], "wizard", $res[0]);
	else
		activate_did($res[1]);
}

function keys($wizard = false)
{
	global $method;
	$method  = "keys";

	//$prompt = Model::selection("prompt",array("status"=>"online"));
	//if(!count($prompt))
	//	return;
	//$prompt_id = $prompt[0]->prompt_id;
	$key = new Key;
	$key->extend(array("status"=>"prompts"));
	$keys = $key->extendedSelect(NULL,"status,key");

	if($wizard) {
		print '<br/>';
		if(count($key))
			set_step(2,"Define keys","complete");
		else
			set_step(2,"Define keys","incomplete",0);
	}

	if(count($keys)){
		$actions = array("&method=edit_key"=>'<img src="images/edit.gif" title="Edit" alt="edit"/>', "&method=delete_key"=>'<img src="images/delete.gif" title="Delete" alt="delete"/>');
		tableOfObjects($keys, array("status", "key","destination","description"), "key for auto attendant", $actions, array("&method=edit_key"=>"Add key"));
	}else
		edit_key();

	return count($keys);
}

function edit_key($status = "online", $error = NULL)
{
	global $method;
	$method = "edit_key";

	if($error)
		errornote($error);

	print '<br /><font class="error">Note !!</font> The keys must match the uploaded prompts.<br /><br />';

	$key = new Key;
	$key->key_id = getparam("key_id");
	$key->select();

	$statuss = array("online", "offline");
	if(!$key->key_id) {
		$statuss["selected"] = $status;
		$key->key = getparam("key");
		$key->destination = getparam("destination");
		$key->description = getparam("description");
		if(getparam("status") && getparam("status") != "Not selected")
			$statuss["selected"] = getparam("status");
	} else {
		$prompt = new Prompt;
		$prompt->prompt_id = $key->prompt_id;
		$prompt->select();
		$statuss["selected"] = $prompt->status;
	}

	$all_groups = Model::selection("group",NULL,"\"group\"");
	$groups = Model::objectsToArray($all_groups,array("group_id"=>"", "group"=>""),true);
	for($i=0; $i<count($all_groups); $i++)
	{
		if($all_groups[$i]->extension == $key->destination) {
			$groups["selected"] = $all_groups[$i]->group_id;
			break;
		}
	}

	$number = (!isset($groups["selected"])) ? $key->destination : NULL;

	$keys = array(0,1,2,3,4,5,6,7,8,9);
	$keys["selected"] = $key->key;

	$fields = array(
					"key" => array($keys, "display"=>"select", "compulsory"=>true,"comment"=>"Prompt will be like: Press 1 for Sales, press 2 for Support."),
					"group" => array($groups, "display"=>"select", "comment"=>"If key points to a group select one from this list. You have to select a group or insert a number in the below field.", "javascript"=>'onChange="comute_destination(\'group\');"'),
					"number" => array("value"=>$number,"comment"=>"Numeric. Ex: 090(extension) or 40744224422(phone number).", "javascript"=>'onClick="comute_destination(\'number\');"'),
					"status" => array("compulsory"=>true,$statuss, "display"=>"select", "comment"=>"Select status of Auto Attendant for which you wish to define a key"),
					"description" => array("display"=>"textarea")
				);

	$title = ($key->key_id) ? "Edit key for ".strtoupper($status)." AutoAttendant" : "Add key for ".strtoupper($status)." AutoAttendant";

	start_form();
	addHidden("database", array("key_id"=>$key->key_id, "prompt_id"=>$key->prompt_id));
	editObject($key,$fields,$title,"Save");
	end_form();
}

function edit_key_database()
{
	$key = new Key;
	$key->key_id = getparam("key_id");

	$status = getparam("status");
	if(!$status || $status == "Not selected") {
		edit_key(NULL,"You have to set the status of the Auto Attendant in order to add a key.");
		return;
	}
	$prompt = Model::selection("prompt", array("status"=>$status));
	if(!count($prompt))
	{
		errormess("You should add the prompts before definning the keys");
		return;
	}
	$prompt_id = $prompt[0]->prompt_id;
	$key->key = getparam("key");

	if(!$key->key && $key->key !== "0")
	{
		edit_key($status,"Field 'Key' is required.");
		return;
	}
	if(Numerify($key->key) == "NULL")
	{
		edit_key($status, "Field 'Key' must be numeric");
		return;
	}
	$key->prompt_id = $prompt_id;
	if($key->objectExists())
	{
		edit_key($status,"This key was previously inserted.");
		return;
	}
	$key->select();
	$key->key = getparam("key");
	$key->prompt_id = $prompt_id;

	$group_id = getparam("group");
	if($group_id && $group_id != "Not selected") {
		$group = new Group;
		$group->group_id = $group_id;
		$group->select();
		$key->destination = $group->extension;
	}elseif(($number = getparam("number"))){
		if(Numerify($number) == "NULL"){
			edit_key($status, "Field number must be numeric");
			return;
		}
		$key->destination = $number;
	}else{
		edit_key($status, "You must select a group or insert a number");
		return;
	}

	$key->description = getparam("description");
	if($key->key_id)
		notify($key->update());
	else
		notify($key->insert());
}

function delete_key()
{
	ack_delete("key", getparam("key"), NULL, "key_id", getparam("key_id"));
}

function delete_key_database()
{
	$key = new Key;
	$key->key_id = getparam("key_id");
	if(!$key->key_id) 
	{
		errormess("Don't have key id in order to delete key");
		return;
	}
	notify($key->objDelete());
}

function scheduling($error = NULL,	$wizard=false)
{
	global $method;
	$method = "scheduling";
	if($error)
		errornote($error);

	$prompt = Model::selection("prompt", array("status"=>"online"));
	if(!count($prompt))
		$prompt_id = NULL;
	else
		$prompt_id = $prompt[0]->prompt_id;
	$days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	for($i=0; $i<count($days); $i++)
	{
		$fields[$days[$i]] = array("value"=>"$prompt_id","display"=>"set_period");
	}
	if($wizard) 
	{
		$time_frame = new Time_Frame;
		$nr = $time_frame->fieldSelect("count(*)");
		if($nr) 
			set_step(3,"Schedule online Auto Attendant","complete",1);
		else
			set_step(3,"Schedule online Auto Attendant","incomplete",1);
	}

	start_form(NULL,NULL,false,"wizard2");
	addHidden("database", array(/*"iframe"=>"true"*/));
	editObject(NULL,$fields, "Scheduling online Auto Attendant ","Save",false,false,"edit",NULL,array("left"=>"160px","right"=>"290px"));
	end_form();
}

function set_wiz_period($step_nr, $field_name, $required = false)
{
	$_SESSION["fields"][$step_nr]["start_".$field_name] = getparam("start_".$field_name);
	$_SESSION["fields"][$step_nr]["end_".$field_name] = getparam("end_".$field_name);

	return true;
}

// if function is called from wizard.php then it will be probably called with the $fields params, since info 
// is held in $_SESSION
function scheduling_database($fields = NULL)
{
	$prompt = Model::selection("prompt", array("status"=>"online"));
	if(!count($prompt)) {
		scheduling("You first need to upload the prompts");
		return;
	}
	$prompt = $prompt[0];
	$days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

	for($i=0; $i<count($days); $i++)
	{
		if(!$fields) {
			$start = getparam("start_".$days[$i]);
			$end = getparam("end_".$days[$i]);
		}else{
			$start = $fields["start_".$days[$i]];
			$end = $fields["end_".$days[$i]];
		}
		if($start=="Not selected" || $end=="Not selected") {
			$start = '';
			$end = '';
		}
		$time_frame = new Time_Frame;
		$time_frame->select(array("prompt_id"=>$prompt->prompt_id, "day"=>$days[$i]));
		$time_frame->prompt_id = $prompt->prompt_id;
		$time_frame->day = $days[$i];
		$time_frame->start_hour = $start;
		$time_frame->end_hour = $end;
		$time_frame->numeric_day = $i;
		if($time_frame->time_frame_id)
			$time_frame->update();
		else
			$time_frame->insert();
	}

	if(isset($_SESSION["wiz_config"]))
		return;

	wizard();
}

function set_period($value, $name)
{
	$time_frame = Model::selection("time_frame", array("prompt_id"=>$value, "day"=>$name));
	if(count($time_frame))
	{
		$start = $time_frame[0]->start_hour;
		$end = $time_frame[0]->end_hour;
	}else{
		$start = 0;
		$end = 0;
	}

	print 'From <select name="start_'.$name.'">';
	print '<option>Not selected</option>';
	for($i=1; $i<25; $i++) {
		print '<option';
		if($start == $i)
			print " SELECTED";
		print '>'.$i.'</option>';
	}
	print '</select>';
	print '&nbsp;&nbsp;To <select name="end_'.$name.'">';
	print '<option>Not selected</option>';
	for($i=1; $i<25; $i++){
		print '<option';
		if($end == $i)
			print " SELECTED";
		print '>'.$i.'</option>';
	}
	print '</select>';
}

function prompts($wizard=false)
{
	$prompts = Model::selection("prompt",NULL,"status DESC");

	if($wizard) {
		if(count($prompts) >= 2)
			set_step(1,"Set prompts for Auto Attendant","complete");
		else
			set_step(1,"Set prompts for Auto Attendant","incomplete");
		}
	if(!count($prompts))
	{
		print "The first step for setting the Auto Attendant is to define the prompts. There are two types of prompts: 'Online' and 'Offline'.<br/><br/>";

		upload_prompts();
		return;
	}

	$actions = array("&method=edit_prompt"=>'<img src="images/edit.gif" alt="edit" title="Edit"/>', "&method=listen_prompt"=>'<img src="images/listen.gif" alt="play" title="Listen"/>', "&method=reupload_prompt"=>'<img src="images/upload.gif" alt="upload" title="Upload"/>');

	tableOfObjects($prompts, array("status", "prompt", "function_getfilesize:size"=>"file"), "prompt", $actions);

	return count($prompts);
}

function getfilesize($prompt)
{
	global $target_path;
	$path = "$target_path/auto_attendant/$prompt";
	$filesize = filesize($path);
	return bytestostring($filesize,2);
}

function upload_prompts($error=NULL)
{
	global $method;
	$method = "upload_prompts";

	if($error)
		errornote($error);

	$fields = array(
					"online" => array("display"=>"file", "comment"=>"Accepted format .mp3. Upload prompt for online Auto Attendant."),
					"offline" => array("display"=>"file", "comment"=>"Accepted formats .mp3. Upload prompt for offline Auto Attendant.")
				);

	start_form(NULL,"post",true);
	addHidden("database",array(/*"iframe"=>"true",*/ "MAX_FILE_SIZE"=>"10000000000"));
	editObject(NULL,$fields, "Upload prompts Auto Attendant", "Save");
	end_form();
}

function reupload_prompt_database()
{
	global $target_path;

	if(!getparam("prompt_id")) {
		errormess("Don't have prompt id");
		return;
	}

	$filename = basename($_FILES["prompt"]["name"]);
	if(strtolower(substr($filename,-4)) != ".mp3")
	{
		reupload_prompt("File format must be .mp3");
		return;
	}	
	$path = "$target_path/auto_attendant";

	if(!is_dir($path))
		mkdir($path);

	$prompt = new Prompt;
	$prompt->prompt_id = getparam("prompt_id");
	$prompt->select();

	$time = date("Y-m-d:H:i:s");
	$file = "$path/".$prompt->status."_".$time."-ns.mp3";
	$resampled = "$path/".$prompt->status."_".$time.".mp3";

	$prompt->file = $prompt->status."_".$time.".mp3";
	$prompt->prompt = $filename;
	$prompt->description = getparam("description");

	if(is_file("$path/".$prompt->file))
		unlink("$path/".$prompt->file);
	$slin = str_replace(".mp3",".slin","$path/".$prompt->file);
	if(is_file($slin))
		unlink($slin);

	if (!move_uploaded_file($_FILES["prompt"]['tmp_name'],$file)) {
		errormess("Could not upload file.");
		return;
	}

//	$au = str_replace(".wav",".au",$file);
//	passthru("sox $file -r 22000 -c 1 -b 16 -A $au");
	$slinfile = str_ireplace(".mp3", ".slin", $resampled);
	passthru("madplay -q --no-tty-control -m -R 8000 -o raw:\"$slinfile\" \"$file\"");
	passthru("$target_path/mp3resample.sh \"$resampled\" \"$file\"");

	notify($prompt->update());
}

function upload_prompts_database()
{
	global $target_path;

	$online = basename($_FILES['online']['name']);
	$offline = basename($_FILES['offline']['name']);

	if(strtolower(substr($online,-4)) != ".mp3" || strtolower(substr($offline,-4)) != ".mp3")
	{
		wizard("File format must be .mp3");
		return;
	}

	$prompts = array("online"=>$online, "offline"=>$offline);

	$path = "$target_path/auto_attendant";

	if(!is_dir($path))
		mkdir($path);

	$time = date("Y-m-d_H:i:s");

	$online_file = "$path/online_".$time.".mp3";
	$offline_file = "$path/offline_".$time.".mp3";

	Database::transaction();
	$prompt = new Prompt;
	$nr = $prompt->fieldSelect("count(*)");
	if($nr) 
	{
		Database::rollback();
		errormess("There are already $nr prompts uploaded");
		return;
	}

	if (!move_uploaded_file($_FILES["online"]['tmp_name'],$online_file)) {
		Database::rollback();
		errormess("Could not upload file for online mode");
		return;
	}
	if (!move_uploaded_file($_FILES["offline"]['tmp_name'],$offline_file)) {
		Database::rollback();
		errormess("Could not upload file for offline mode");
		return;
	}

	$slin_online = str_ireplace(".mp3",".slin",$online_file);
	$slin_offline = str_ireplace(".mp3",".slin",$offline_file);

//	passthru("sox $online_file -r 8000 -c 1 -b -A $auonline");

//	passthru("sox $offline_file -r 8000 -c 1 -b -A $auoffline");

	passthru("madplay -q --no-tty-control -m -R 8000 -o raw:\"$slin_online\" \"$online_file\"");
	passthru("madplay -q --no-tty-control -m -R 8000 -o raw:\"$slin_offline\" \"$offline_file\"");

	if(!is_file($slin_online) || !is_file($slin_offline)) {
		Database::rollback();
		errormess("Could not convert files in .au format.");
		return;
	}

	foreach($prompts as $status=>$prompt_name)
	{
		$prompt = new Prompt;
		$prompt->prompt = $prompt_name;
		$prompt->status = $status;
		$prompt->file = $status."_".$time.".mp3";
		$res = $prompt->insert();
		if(!$res[0]) {
			Database::rollback();
			errormess("Could not upload the prompts. Please try again");
			return;
		}
	}
	Database::commit();
	wizard();
}

function reupload_prompt($error = NULL)
{
	if($error)
		errornote($error);

	$prompt = new Prompt;
	$prompt->prompt_id = getparam("prompt_id");
	$prompt->select();

	if(!$prompt->prompt_id) {
		errormess("Don't have prompt_id. Can't re-upload this prompt.");
		return;
	}

	$statuss = array("online", "offline");
	$statuss["selected"] = $prompt->status;
	$fields = array(
					"prompt"=>array("display"=>"file"),
					"status"=>array("display"=>"fixed"),
					"description"=>array("display"=>"textarea")
				);
	
	start_form(NULL,NULL,true);
	addHidden("database",array("prompt_id"=>getparam("prompt_id")));
	editObject($prompt,$fields, "Upload prompts for each Auto Attendant Mode", "Save");
	end_form();
}

function edit_prompt()
{
	$prompt = new Prompt;
	$prompt->prompt_id = getparam("prompt_id");
	$prompt->select();

	$fields = array(
					"prompt" =>array("comment"=>"Name of the file. Please don't change extension", "compulsory"=>true),
					"status" => array("display"=>"fixed"),
					"description" => array("display"=>"textarea")
				); 

	start_form();
	addHidden("database", array("prompt_id"=>$prompt->prompt_id));
	editObject($prompt,$fields,"Edit prompt","Save",true);
	end_form();
}

function edit_prompt_database()
{
	global $target_path;

	$prompt = new Prompt;
	$prompt->prompt_id = getparam("prompt_id");
	$prompt->select();

	if(!$prompt->status) {
		errormess("Don't have the status attribute for this prompt.");
		return;
	}
	$new_name = getparam("prompt");
	if(!$new_name) {
		errormess("Field 'Prompt' is compulsory");
		return;
	}

	$prompt->prompt = $new_name;
	$prompt->description = getparam("description");
	notify($prompt->update());
}

function listen_prompt()
{
	global $target_path;

	$filepath =  $target_path. "/auto_attendant/";

	$prompt = new Prompt;
	$prompt->prompt_id = getparam("prompt_id");
	$prompt->select();

	editObject($prompt, array("prompt"=>array("display"=>"fixed"), "size"=>array("value"=>getfilesize($prompt->file), "display"=>"fixed")), "Playing prompt for ".$prompt->status." Auto Attendant","Save");
	//$filename = str_replace(".wav", ".au", $prompt->prompt);

	$filepath .= $prompt->file;

	if(!is_file($filepath))
	{
		errormess("Missing file");
		return;
	}
?>
	<center>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="450" height="40" id="home" align="center">
		<param name="movie" value='flash_movie.php?size=160&nostart=true&mp3=<?php print $filepath;?>' />
		<param name="quality" value="high" />
		<embed src='flash_movie.php?size=160&nostart=true&mp3=<?php print $filepath;?>' quality="high" width="450" height="40"  name="home" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	</center>
<?php
}
?>