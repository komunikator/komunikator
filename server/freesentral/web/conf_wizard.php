<?php
/**
 * conf_wizard.php
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
global $logo, $title, $steps, $trigger_name;

$logo = "images/small_logo.png";
$title = "Configuring FreeSentral";
$trigger_name = "add";
//where uploaded files will be stored until Finish is pressed
$upload_path = "/tmp";

// array defining the steps 
// this example has 8 steps: from 0 to 7

/*
Special fields: 
"step_image", image that should be put in the left box. Ex: "images/welcome.jpg"
"step_name", name of this step. It will be printed on top of the fiels that will be setted 
"step_description", a description on this step(it will be put in the left box, under the step_image, if setted)
"on_submit", name of the javascript function that should be called to verify inserted content after pressing Next button
"upload_form", is true, then this form can be used for uploading
 
The array that is used for defining a field:
array(
	//default value for the field
	"value" => ''
	//way field should be displayed: 
text(default, in case display is not specified), 
fixed(value will be printed but field won't be editable), 
checkbox
radio(radios)
textarea
select (in this case defining array [0] must be an array that will define the options for the drop down
password
file
hidden
In none of the above but field is setted: a function with that name will be called
	"display" => ''
	"advanced" => '' //when setted field would be marked as advanced (and will be shown only after pressing the show advanced fields)
	"triggered_by" =>x,  x is a numeric identifier. Will make this field to be shown (in must be used with another field of type message that has a tag(img, a etc.) with an event that will call javascript  show_fields(x)), Another note: field name must end with x, and name of the field where show fields is called must be something like this: "$trigger_name" . (x-1)
	"javascript" => '' // javascript that is called on a certain event for this field Ex: onClick="show_advanced();"
	"comment" => '' // comment that will be printed under the field (after pressing on the ? image)
	 "custom_submit" => '' // name of custom function that should be called to set this field in $_SESSION
)
*/
$steps = array(
		array(
			// image that should be put in the left box
			// ex: "images/welcome.jpg"
			"step_image" => "",
			// name of this step. It will be printed on top of the fiels that will be setted
			"step_name" => "Welcome!",
			// a description on this step.
			// it will be put in the left box, under the step_image, if setted
			"step_description" => "",
			// name of field => definition (value => value of this field(in case there is a default one), "display" => how should this field be displayed: in this case display=>"message" will print a message and name of field is going to be ignored)
			"message" => array(
				"value"=> "You can use this wizard to configure your telephony server. If you are an experienced user you can skip these steps, else we recommend that you go throught these simple steps.",
				"display"=>"message"
			)
		),
		array(
			"step_image" => "",
			"step_description" => "",
			// name of the javascript function that should be called to verify inserted content after pressing Next button
			"on_submit" => "verify_password",
			"message" => array(
				"value"=> "You need to change the password for the default admin of this system. If you already changed this password press 'Skip', if not please insert the new password.",
				"display"=>"message"
			),
			"new_password" => array(
				// information entered will be displayed as a password
				"display"=>"password", 
				//  this field is marked as compulsory
				"compulsory"=>true, 
				//  explanation about the field
				"comment"=>"At least 5 digits long.",
				"autocomplete"=>"off"
			),
			"retype_new_password" => array(
				"display"=>"password", 
				"compulsory"=>true,
				"autocomplete"=>"off"
			)
		),
		array(
			"step_image" => "images/extension.png",
			"step_name" => "Extensions",
			"step_description" => "Extensions - Internal phones attached to the IP PBX.",
			"on_submit" => "verify_extensions",
			"from" => array(
				"value"=>"", 
				"compulsory"=>true, 
				"comment"=>"Numeric value. Minimum 3 digits"
			),
			"to"=>array(
				"value"=>"", 
				"compulsory"=>true, 
				"comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be the same number of digits as the 'From' field."
			),
			"message" => array(
				"display" => "message",
				"value" => "If you don't want to define the extensions as a range you can press 'Skip' and define them from the 'Extensions' tab after using the Wizard."
			)
		),
		array(
			"step_image" => "images/extension.png",
			"step_name" => "Groups",
			"step_description" => "Groups - Organize your extensions in groups. You can have as many extensions as you want in a group. An extension can join more than one group.",
			"on_submit" => "verify_groups",
			"group" => array(
				"compulsory" => true,
				"comment" => "Name of the group."
			),
			"extension" => array(
				"compulsory" => true,	
				"comment" => "Number one needs to call to reach this group. <br/>Ex: 01 for Sales(Must be 2 digits long)"
			),
			"members" => array(
				"comment" => "The extensions that are in this group. Insert them separated by commas. Ex: 090,091,092,100. If you have a range, you can use the below To-From fields in order to add them all to the group. "
			),
			"from" => array(
				"comment" => "Numeric value. Must be in the range defined in the previous step."
			),
			"to" => array(
				"comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be in the range defined in the previous step."
			),
			// this will print a link: Add another group, when one clicks on it fields group2, extensionn2, members2, from2, to2, add2 will be displayed after link is preseed
			// Note!! field name is add1, note the 1,  show_fields is called with 2 and all the fields that will be displayed end with 2
			"add1" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('2');\">Add another group</font>"
			),
			"group2" => array(
				"compulsory" => true,
				"comment" => "Name of the group.",
				"triggered_by" => "2"
			),
			"extension2" => array(
				"compulsory" => true,	
				"comment" => "Number one needs to call to reach this group. <br/>Ex: 01 for Sales(Must be 2 digits long)",
				"triggered_by" => "2"
			),
			"members2" => array(
				"comment" => "The extensions that are in this group. Insert them separated by commas. Ex: 090,091,092,100. If you have a range, you can use the below To-From fields in order to add them all to the group. ",
				"triggered_by" => "2"
			),
			"from2" => array(
				"comment" => "Numeric value. Must be in the range defined in the previous step.",
				"triggered_by" => "2"
			),
			"to2" => array(
				"comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be in the range defined in the previous step.",
				"triggered_by" => "2"
			),
			"add2" => array(
				"display" => "message",
				"value" => "<font class=\"wizlink\" onClick=\"show_fields('3');\">Add another group</font>",
				"triggered_by" => "2"
			),
			"group3" => array(
				"compulsory" => true,
				"comment" => "Name of the group.",
				"triggered_by" => "3"
			),
			"extension3" => array(
				"compulsory" => true,	
				"comment" => "Number one needs to call to reach this group. <br/>Ex: 01 for Sales(Must be 2 digits long)",
				"triggered_by" => "3"
			),
			"members3" => array(
				"comment" => "The extensions that are in this group. Insert them separated by commas. Ex: 090,091,092,100. If you have a range, you can use the below To-From fields in order to add them all to the group. ",
				"triggered_by" => "3"
			),
			"from3" => array(
				"comment" => "Numeric value. Must be in the range defined in the previous step.",
				"triggered_by" => "3"
			),
			"to3" => array(
				"comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be in the range defined in the previous step.",
				"triggered_by" => "3"
			),
			"add3" => array(
				"display" => "message",
				"value" => "<font class=\"wizlink\" onClick=\"show_fields('4');\">Add another group</font>",
				"triggered_by" => "3"
			),
			"group4" => array(
				"compulsory" => true,
				"comment" => "Name of the group.",
				"triggered_by" => "4"
			),
			"extension4" => array(
				"compulsory" => true,	
				"comment" => "Number one needs to call to reach this group. <br/>Ex: 01 for Sales(Must be 2 digits long)",
				"triggered_by" => "4"
			),
			"members4" => array(
				"comment" => "The extensions that are in this group. Insert them separated by commas. Ex: 090,091,092,100. If you have a range, you can use the below To-From fields in order to add them all to the group. ",
				"triggered_by" => "4"
			),
			"from4" => array(
				"comment" => "Numeric value. Must be in the range defined in the previous step.",
				"triggered_by" => "4"
			),
			"to4" => array(
				"comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be in the range defined in the previous step.",
				"triggered_by" => "4"
			),
		),
		array(
			"step_image" => "images/gateways.png",
			"step_name" => "Outbound: Gateway and default Dial Plan",
			"step_description" => "Gateway: the connection to another FreeSentral, other PBX or network. It is the address you choose your call to go to. <br/><br/> 
Dial Plan: to define a dial plan means to make the connection between a call and a gateway. You have the option to direct calls of your choice to go to a specified gateway. <br/><br/>
The System's CallerID is the number that will be used as caller number when sending a call outside your system, and System's Callername is the name.<br/><br/>Both can be set per gateway also. If they weren't set per gateway then System CallerID and System Callername will be used.",
			"on_submit" => "verify_gateway",
			"gateway" => array(
				"value"=>"default gateway", 
				"display"=>"fixed", 
				"compulsory"=>true
			), 
			"protocol" => array(
				array("sip", "h323", "iax", "selected"=>"sip"), 
				"display"=>"select", 
				"compulsory"=>true
			),
			"username"=>array(
				"comment"=>"Insert only when you need to register to another gateway in order to send calls.",
				"autocomplete"=>"off"
			), 
			"password"=>array(
				"comment"=>"Insert only when you need to register to another gateway in order to send calls.", "display"=>"password",
				"autocomplete"=>"off"
			),
			"server"=>array(
				"compulsory"=>true, 
				"comment"=>"Ip address of the server. The ports used are the default ones. To change them you can use the Outbound tab."
			),
			"default_dial_plan"=>array(
				"value"=>"t",
				"display"=>"checkbox", 
				"comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallest priority."
			),
			"system_CallerID"=>array(
				"comment"=>"This will be the number used when a call will be made outside your system.",
				"compulsory"=>true
			),
			"system_Callername"=>array(
				"comment"=>"This will be the name used when a call will be made outside your system.",
			),
		),
		array(
			"step_image" => "images/dids.png",
			"step_name" => "Set voicemail",
			"step_description" => "DID: Direct Inward Calling.<br/> Set a DID for your voicemail.",
			"on_submit" => "verify_voicemail",
			/*	"did" => array(
					"column_name" => "DID",
					"value" => "voicemail",
					"display" => "fixed",
				),*/
			"number" => array(
				"column_name" => "DID",
				"compulsory" => true,
				"comment" => "Number to call to listen to voicemail."
			)/*,
			"destination" => array(
				"value" => "external/nodata/voicemaildb.php",
				"display" => "fixed"
			)*/
		),
		array(
			// the form with allow uploading of files
			"upload_form" => true,
			"step_image" => "images/auto-attendant.png",
			"step_name" => "Set Auto Attendant",
			"step_description" => "Auto Attendant: Calls within the PBX are answered and directed to their desired destination by the auto attendant system.<br/><br/>The keys you define must match the prompts you uploaded. If your online prompt says something like this: Press 1 for Sales, then you must select type: online, key: 1, and insert group: Sales (you must have defined Sales in the Groups section in a previous step). Same for offline state. If you want to send a call directly to an extension or another number, you should insert the number in the 'Number(x)' field from Define Keys section.",
			"on_submit" => "verify_auto_attendant",
			"message" => array(
				"value" => "If you don't wish to enable the Auto Attendant press 'Skip'.<br/>",
				"display" => "message"
			),
			/*"did" => array(
				"column_name" => "DID",
				"value" => "auto attendant",
				"display" => "fixed",
			),*/
			"number" => array(
				"column_name" => "DID",
				"compulsory"=>true,
				"comment"=>"Incoming phone number. When receiving a call for this number send call to Auto Attendant."
			),
			/*	"destination" => array(
				"value" => "external/nodata/auto_attendant.php",
				"display" => "fixed"
			),*/
			"extension" => array(
				"compulsory"=>true,
				"comment"=>"The default extension where call will be transfered if caller doesn't press any digit when reaching the Auto Attendant."
			),
			"explain" => array(
				"value" => "The Auto Attendant has two states: online and offline. Each of these states has its own prompt.",
				"display" => "message"
			),
			"online_prompt" => array(	
				"display" => "file",
				"compulsory" => true,
				"comment" => "Accepted format .mp3. Upload prompt for online Auto Attendant."
			),
			"offline_prompt" => array(
				"display" => "file",
				"compulsory" => true,
				"comment" => "Accepted format .mp3. Upload prompt for offline Auto Attendant."
			),
			"explanation_scheduling" => array(
				"value" => "Schedulling online Auto Attendant. The time frames when the online auto attendant is not scheduled, the offline one is used.",
				"display" => "message"
			),
			//Note!! "custom_submit"=>"set_wiz_period", set_wiz_period will be called to set this field in $_SESSION, 
			"Sunday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Monday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Tuesday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Wednesday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Thursday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Friday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),
			"Saturday" => array("display" => "wiz_period", "custom_submit"=>"set_wiz_period"),

			"explain_key" => array(
				"display"=>"message",
				"value" => "Defining Keys"
			),
			"add0" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('1');\">Define key</font>",
			),
			"type1" => array(
				array("online", "offline"),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 1
			),
			"key1" => array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 1
			),
			"number1" => array(
				"comment" => "Insert extension or number if you wish to connect to it when pressing key." ,
				"triggered_by" => 1
			),
			"group1" => array(
				"comment" => "Insert group name. Make sure that you added this group in the Groups section.",
				"triggered_by" => 1
			),
			"add1" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('2');\">Add another key</font>",
				"triggered_by" => 1
			),
			"type2" => array(
				array("online", "offline"),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 2
			),
			"key2" => array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 2
			),
			"number2" => array(
				"comment" => "Insert extension or number if you wish to connect to it when pressing key.",
				"triggered_by" => 2 
			),
			"group2" => array(
				"comment" => "Insert group name. Make sure that you added this group in the Groups section.",
				"triggered_by" => 2 
			),
			"add2" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('3');\">Add another key</font>",
				"triggered_by" => 2 
			),
			"type3" => array(
				array("online", "offline"),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 3
			),
			"key3" => array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 3
			),
			"number3" => array(
				"comment" => "Insert extension or number if you wish to connect to it when pressing key." ,
				"triggered_by" => 3
			),
			"group3" => array(
				"comment" => "Insert group name. Make sure that you added this group in the Groups section." ,
				"triggered_by" => 3
			),
			"add3" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('4');\">Add another key</font>",
				"triggered_by"=>3
			),
			"type4" => array(
				array("online", "offline"),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 4
			),
			"key4" => array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 4
			),
			"number4" => array(
				"comment" => "Insert extension or number if you wish to connect to it when pressing key.",
				"triggered_by" => 4 
			),
			"group4" => array(
				"comment" => "Insert group name. Make sure that you added this group in the Groups section.",
				"triggered_by" => 4 
			),
			"add4" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('5');\">Add another key</font>",
				"triggered_by" => 4 
			),
			"type5" => array(
				array("online", "offline"),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 5
			),
			"key5" => array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
				"display" => "select",
				"compulsory" => true,
				"triggered_by" => 5
			),
			"number5" => array(
				"comment" => "Insert extension or number if you wish to connect to it when pressing key." ,
				"triggered_by" => 5
			),
			"group5" => array(
				"comment" => "Insert group name. Make sure that you added this group in the Groups section.",
				"triggered_by" => 5
			)
			/*"add5" => array(
				"display" => "message",
				"value" => "<font id=\"add1\" class=\"wizlink\" onClick=\"show_fields('6', 'add1');\">Add another key</font>",
				"triggered_by" => 5
			),*/
		)
		/*,array(
			"upload_form" => true,
			"step_name" => "Music on hold",
			"step_description" => "Upload the music files for your default playlist that will be used for Music on hold on your system.",
			"on_submit" => "verify_moh",
			"messs" => array(
				"display"=>"message",
				"value"=>"File format must be .mp3"
			),
			"file1" => array(
				"display"=>"file"
			),
			"file2" => array(
				"display"=>"file"
			),
			"file3" => array(
				"display"=>"file"
			),
			"file4" => array(
				"display"=>"file"
			),
			"file5" => array(
				"display"=>"file"
			)
		)*/
	);
?>