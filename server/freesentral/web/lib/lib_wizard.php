<?php
/**
 * lib_wizard.php
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
global $trigger_name, $upload_path;
/**
 *	Class that will be used to define a wizard
 */
class Wizard
{
	public $logo;
	public $steps;
	public $title;
	public $finished_setting;
	public $fields;
	public $step_nr;
	public $error = '';
	public $finished_settings = false;
	public $on_finish = "main.php";
	public $ending_message;
	public $have_description = true;

	public $reserved_names = array("step_description"=>"", "step_image"=>"", "step_name"=>"", "upload_form"=>"", "on_submit"=>"");

	/**
	 * Construct an object of type Wizard, handle the verifications,passing to the next step, call function that takes information from $_SESSION and sets the info in the database
	 * @param $_steps Array that should be set in conf_wizard.php that defines all steps
	 * @param $_logo Text, path and name of the image used as logo
	 * @param $_title Text, title of the wizard, will be displayed on the same line as the logo, during all steps 
	 * @param $function_for_finish Name of the function that should be called after Finish is pressed
	 * @param $on_finish Where to go when the 'Close'(button after settings are done) is pressed. Ex: "main.php?module=HOME". This is also the default value of the field
	 */
	function __construct($_steps, $_logo, $_title, $function_for_finish, $on_finish=null, $mess_on_finish=null, $have_description=true)
	{
		$this->steps = $_steps;
		$this->logo = $_logo;
		$this->title = $_title;
		if($on_finish)
			$this->on_finish = $on_finish;
		$this->ending_message = $mess_on_finish;
		$this->have_description = $have_description;
		
		if(!isset($_SESSION["wizard_step_nr"]))
			$_SESSION["wizard_step_nr"] = 0;
		$this->step_nr = $_SESSION["wizard_step_nr"];

		if(getparam("submit") == "Next") {
			// set the information that was setted in the previous step
			$this->setStep();
			if($this->error == '')
				$this->incStep();
		}elseif(getparam("submit") == "Previous")
			$this->decStep();
		elseif(getparam("submit") == "Skip" && ($this->step_nr < (count($this->steps)-1)))
		{
			$this->setStep(true); // set step allowing variables to be null, even if they were required for the completion of this step
			if ($this->error == '')
				$this->incStep();
		}elseif(getparam("submit") == "Finish" || ($this->step_nr == (count($this->steps) -1) && getparam("submit") != "Retry")) {
			if(getparam("submit") == "Skip")
				$this->setStep(true);
			else
				$this->setStep();
			// $function_for_finish() must return something like array(true/false, $message) where true shows that the process is finished, while false shows that it's wasn't finished, $message is a message returned by the function that will be printed
			if($this->error == '')
				$this->finished_settings = $function_for_finish();
		}elseif(getparam("submit") == "Retry") {
			//print '<br/><br/>submit is Retry<br/><br/>';
			// do nothing :)
		}
		if(!$this->finished_settings)
			$this->loadStep();

		$this->htmlFrame();
	}

	/**
	 * Load a certain step: take the fields from the current step number from the configuration array,
	 * and if any of the fields were already set, modify the fields accordingly
	 * In this step default fields and those that have "display"=>"message" are ignored
	 * In case "display"=>"file" and files were already uploaded and a "fake_".fieldname of type hidden in added that will prevent the error that field is required, in case it is.
	 */
	function loadStep()
	{
		global $trigger_name;

		// load the array of fields from the conf file for this step
		$fields = $this->steps[$this->step_nr];
		// set the reserved fields for this step (image, description, title) and stem remove them from the lists of fields
		foreach($this->reserved_names as $reserved_field_name=>$reserved_field_value)
		{
			$this->reserved_names[$reserved_field_name] = (isset($fields[$reserved_field_name])) ? $fields[$reserved_field_name] : '';
			if(isset($fields[$reserved_field_name]))
				unset($fields[$reserved_field_name]);
		}

		if(isset($_SESSION["fields"][$this->step_nr])) {
			foreach($fields as $field_name=>$field_description) {
				if(!isset($this->steps[$this->step_nr][$field_name]))
					continue;
				if(isset($field_description["display"])) { 
					if($field_description["display"] == "message" || $field_description["display"] == "fixed")
						continue;
					if($field_description["display"] == "file") {
						 $fields[$field_name]["value"] = (isset($_SESSION["fields"][$this->step_nr][$field_name]["orig_name"])) ? $_SESSION["fields"][$this->step_nr][$field_name]["orig_name"] : '';
						if($fields[$field_name]["value"] != '') {
							$fields["fake_".$field_name] = array();
							$fields["fake_".$field_name]["value"] =  $fields[$field_name]["value"];
							$fields["fake_".$field_name]["display"] = "hidden";
						}
						continue;
					}
					if($field_description["display"] == "mul_select") {
						$saved_field_name = str_replace("[]","",$field_name);
						$fields[$field_name][0]["selected"] = (isset($_SESSION["fields"][$this->step_nr][$saved_field_name])) ? $_SESSION["fields"][$this->step_nr][$saved_field_name] : '';
						continue;
					}
				}

				$fields[$field_name]["value"] = (isset($_SESSION["fields"][$this->step_nr][$field_name])) ? $_SESSION["fields"][$this->step_nr][$field_name] : '';
				if(isset($field_description["display"]))
					if($field_description["display"] == "select")
						if(isset($fields[$field_name][0]))
							$fields[$field_name][0]["selected"] = $fields[$field_name]["value"];
				if($fields[$field_name]["value"] != "")
					if(isset($fields[$field_name]["triggered_by"])) {
						$trigged_by = $fields[$field_name]["triggered_by"];
						$former_trigger =  $trigged_by -1;;
						if(isset($fields[$trigger_name.$former_trigger]))
							unset($fields[$trigger_name.$former_trigger]);
						$fld = $fields;
						foreach($fld as $fldn=>$fldd) {
							if(isset($fldd["triggered_by"]))
								if($fldd["triggered_by"] == $trigged_by)
									unset($fields[$fldn]["triggered_by"]);
						}
						unset($fields[$field_name]["triggered_by"]);
					}
			}
		}
		$this->fields = $fields;
	}

	/**
	 * Set the current step. It takes the information that was setted before submiting 
	 * the page and sets the fields into the session. Checks for required fields and sets 
	 * $this->error in case some required fields are missing
	 * @param $skip Bool value, true when  you wish to skip the verifications with required fields
	 * Note! If files were uploaded they will be moved to $upload_path and in $_SESSION["fields"][$step_nr][$field_name] will be an array with [orig_name] => name the file [path] => path where it was uploaded
	 */
	function setStep($skip = false)
	{
		global $upload_path;

		$fields = $this->steps[$this->step_nr];

		foreach($fields as $field_name=>$field_def)
		{
			if(isset($this->reserved_names[$field_name]))
				continue;
			if(isset($field_def["display"]))
				if($field_def["display"] == "message" || $field_def["display"] == "fixed")
					continue;
			if(!isset($_SESSION["fields"]))
				$_SESSION["fields"] = array();

			if(substr($field_name,-2) == "[]" && $field_def["display"] == "mul_select")
				$field_name = str_replace("[]","",$field_name);

			$set = false;
			$required = false;
			if(isset($field_def["required"]) && ($field_def["required"] === true || $field_def["required"] == "true"))
				$required = true;
			if(isset($field_def["compulsory"]) && ($field_def["compulsory"] === true || $field_def["compulsory"] == "true"))
				$required = true;

			if(isset($field_def["display"]) && $field_def["display"] == "file" && $skip === false) {
				if(!$upload_path) {
					print "<br/>Upload directory is not set. Could not upload. ".$field_name."<br/>";
					continue;
				}

				if(!$_FILES[$field_name]["name"] && isset($_SESSION["fields"][$this->step_nr][$field_name]["path"]))
					continue;

				if(!$_FILES[$field_name]['tmp_name'] && !$required)
					continue;
				elseif(!$_FILES[$field_name]['name']){
					$this->error .= " Couldn't upload $field_name.";
					continue;
				}
				$file = basename($_FILES[$field_name]['name']);
				$new_filename = "$upload_path/$file";
				if(is_file($new_filename)) {
					$parts = explode(".", $new_filename);
					$extension = strtolower($parts[count($parts) - 1]);
					unset($parts[count($parts) - 1]);
					$new_filename = implode(".",$parts);
					$new_filename .= "_";
					$new_filename .= ".".$extension;
				}

				if (!move_uploaded_file($_FILES[$field_name]['tmp_name'],$new_filename))
					$this->error .= " Couldn't upload $field_name.";
				else{
					$_SESSION["fields"][$this->step_nr][$field_name]["orig_name"] = $file;
					$_SESSION["fields"][$this->step_nr][$field_name]["path"] = $new_filename;
				}
				$set = true;
			}
			if(!$set)
				$_SESSION["fields"][$this->step_nr][$field_name] = getparam($field_name);

			if(isset($field_def["custom_submit"]))
				$_SESSION["fields"][$this->step_nr][$field_name] = $field_def["custom_submit"]($this->step_nr, $field_name, $required);

			if($skip === false) {
				if($required === true) {
					if(isset($fields[$field_name]["display"]))
						if($fields[$field_name]["display"] == "file")
							if(!isset($_FILES[$field_name]["name"])) {
								$this->error .= " Field '".ucfirst(str_replace("_"," ",$field_name))."' is required. Please upload file.";	
								continue;
							}
					if(!$_SESSION["fields"][$this->step_nr][$field_name] && !isset($fields[$field_name]["triggered_by"]))
						$this->error .= " Field '".ucfirst(str_replace("_"," ",$field_name))."' is required.";
				}
			}
		}
	}

	/**
	 * Decrement current step_nr
	 */
	function decStep()
	{
		if($this->step_nr > 0)
			$this->step_nr--;
		$_SESSION["wizard_step_nr"] = $this->step_nr;
	}

	/**
	 * Increment current step_nr
	 */
	function incStep()
	{
		$this->step_nr++;
		$_SESSION["wizard_step_nr"] = $this->step_nr;
	}

	/**
	 * Create the form for the current step_nr
	 * First step has : 2 submits: Next and Skip (is skip is pressed then page is redirected to home page, if setted)
	 * All steps exept last one: 3 submits: Previous, Next(if pressed the javascript function can be called if set), Skip (if presses then error for required fields won't be printed)
	 * Last step: Previous, Finish, Skip (both Finish and Skip submit all pages just that on Finish javascript to verify fields can be called)
	 */
	function htmlFrame()
	{
		$fields = $this->fields;

		if($this->reserved_names["upload_form"] != "")
			start_form(NULL, "post", true);
		else
			start_form();
		addHidden();
		$fin = $this->finished_settings;

		if(!$fin) {
			print '<table class="wizard" cellspacing="0" cellpadding="0">';
			print '<tr>';
			$colspan = ($this->have_description) ? 'colspan="2"' : "";
			print '<td class="fillall" '.$colspan.'>';
			print '<table class="fillall" cellspacing="0" cellpadding="0">';
			print '<tr>';
			print '<td class="logo_wizard">';
			if($this->logo)
				print '<img src="'.$this->logo.'">';
			print '</td>';
			print '<td class="title_wizard">';
			if(isset($this->title))
				print '<div class="title_wizard">'.$this->title.'</div>';
			print '</td>';
			print '</tr>';
			print '</table>';
			print '</td>';
			print '</tr>';
			print '<tr>';
			if ($this->have_description)
			{
				print '<td class="wiz_description">';
				if($this->reserved_names["step_image"] != '' || $this->reserved_names["step_description"] != '')
				{
					print '<table class="wizard_step_description" cellspacing="0" cellpadding="0">';
					if($this->reserved_names["step_image"] != '')
					{
						print '<tr><td class="step_image">';
						print '<img src="'.$this->reserved_names["step_image"].'" />';
						print '</td></tr>';
					}
					if($this->reserved_names["step_description"] != '') 
					{
						print '<tr><td class="step_description">';
						print $this->reserved_names["step_description"];
						print '</td></tr>';
					}
					print '</table>';
				}
				print '</td>';
				$css = "";
			} else {
				$css = "fillall";
			}
			print '<td class="wizard_content '.$css.'">';
			print '<table class="wizard_content fillall" cellspacing="0" cellpadding="0">';
			print '<tr>';
			print '<th class="wizard_content">';
			print $this->reserved_names["step_name"];
			print '</th>';
			print '</tr>';
			print '<tr>';
			print '<td class="wizard_content fillall">';
			print '<table class="wizard_fields fillall" cellspacing="0" cellpadding="0">';

			if($this->error != '') {
				print '<tr>';
				print '<td colspan="2" class="wizard_error">';
				errormess($this->error, 'no');
				$errors = array();
				set_error_fields($this->error, $errors);
				print '</td>';
				print '</tr>';
				for ($i=0; $i<count($errors); $i++)
					$fields[$errors[$i]]["error"] = true;
			}
			foreach($fields as $field_name=>$field_format)
				display_pair($field_name, $field_format, null, null, 'wizedit', false, NULL, NULL);
			print '</table>';

			print '<table class="fillall wizard_submit" cellspacing="0" cellpadding="0">';
			print '<tr>';
			print '<td class="fillall wizard_submit">';
			if($this->step_nr != 0) {
				print '<input type="submit" name="submit" value="Next" style="visibility:hidden; width:0px; height:0px; border:none;">&nbsp;&nbsp;';
				print '<input type="submit" name="submit" value="Previous">&nbsp;&nbsp;';
			}
			if($this->step_nr < (count($this->steps)-1)) {
				if($this->reserved_names["on_submit"] == '')
					print '<input type="submit" name="submit" value="Next"/>&nbsp;&nbsp;';
				else
					print '<input type="submit" name="submit" value="Next" onClick="return on_submit(\''.$this->reserved_names["on_submit"].'\');">&nbsp;&nbsp;';
		//		if($this->step_nr == 0)
		//			print '<input type="button" name="submit" value="Skip" onClick="location.href=\'main.php\'">';
		//		else
					print '<input type="submit" name="submit" value="Skip"/>';
			}else{
				if($this->reserved_names["on_submit"] == '')
					print '<input type="submit" name="submit" value="Finish">&nbsp;&nbsp;';
				else
					print '<input type="submit" name="submit" value="Finish" onClick="return on_submit(\''.$this->reserved_names["on_submit"].'\')">&nbsp;&nbsp;';
				print '<input type="submit" name="submit" value="Skip"/>';
			}
			print '</td>';
			print '</tr>';
			print '</table>';
			print '</td>';
			print '</tr>';
			print '</table>';
			print '</td>';
			print '</tr>';
			print '</table>';
		}else{
			print '<table class="wizard" cellspacing="0" cellpadding="0">';
			print '<tr>';
			print '<td class="fillall" colspan="2">';
			print '<table class="fillall" cellspacing="0" cellpadding="0">';
			print '<tr>';
			print '<td class="logo_wizard">';
			if(isset($this->logo))
				print '<img src="'.$this->logo.'">';
			print '</td>';
			print '<td class="title_wizard">';
			if(isset($this->title))
				print '<div class="title_wizard">'.$this->title.'</div>';
			print '</td>';
			print '</tr>';
			print '</table>';
			print '</td>';
			print '</tr>';
			if($fin[0]) {
				print '<tr>';
				print '<td class="fillall" colspan="2">';
				$mess = ($this->ending_message) ? $this->ending_message : "The wizard has finished configuring your system.";
				print '<br/><br/>'.$mess.'<br/><br/>';
				print $fin[1];
				print '<br/><br/>';
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fillall wizard_submit" colspan="2">';
				print '<input type="button" name="submit" value="Close" onClick="location.href=\''.$this->on_finish.'\'">';
				print '</td>';
				print '</tr>';
				unset($_SESSION["fields"]);
				unset($_SESSION["wizard_step_nr"]);
			}else{
				print '<tr>';
				print '<td class="fillall" colspan="2">';
				print '<br/><br/>Couldn\'t finish configuring this system.<br/><br/>';
				errormess($fin[1], "no");
				$_SESSION["wizard_step_nr"] = $fin[2];
				print '<br/><br/>';
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<td class="fillall wizard_submit" colspan="2">';
				print '<input type="submit" name="submit" value="Retry" />&nbsp;&nbsp;';
				print '<input type="button" name="submit" value="Close" onClick="location.href=\'main.php\'">';
				print '</td>';
				print '</tr>';
			}	
			print '</table>';
		}
		end_form();
	}
}


?>