<?php
/**
 * lib.php
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
global $module, $method, $action, $vm_base, $limit, $db_true, $db_false, $limit, $page;

function include_classes()
{
	$handle = opendir('classes/');
	while (false !== ($file = readdir($handle))) {
		if (substr($file,-4) != '.php')
			continue;
		else
			require_once("classes/$file");
	}
}

if (!function_exists("stripos")) {
	// PHP 4 does not define stripos
	function stripos($haystack,$needle,$offset=0)
	{
		return strpos(strtolower($haystack),strtolower($needle),$offset);
	}
}

escape_page_params();

function testpath($path)
{
	if (ereg("[^A-Za-z0-9_]",$path)) 
	{
		// Client tried to hack around the path naming rules - ALERT!
		forbidden();
    }
}

function forbidden()
{
	header("403 Forbidden");
	session_unset();
	print '<html><body style="color:red">Forbidden</body></html>';
	exit();
}

function start_form($action = NULL, $method = "post", $allow_upload = false, $form_name = NULL)
{
	global $module;

	if(!$method)
		$method = "post";
	$form = (!$module) ? "current_form" : $module;
	if(!$form_name)
		$form_name = $form;
	if(!$action) {
		if(isset($_SESSION["main"]))
			$action = $_SESSION["main"];
		else
			$action = "index.php";
	}

	?><form action="<?php print $action;?>" name="<?php print $form_name;?>" id="<?php print $form_name;?>" method="<?php print $method;?>" <?php if($allow_upload) print 'enctype="multipart/form-data"';?>><?php
}

function end_form()
{
	?></form><?php
}

function note($note)
{
	print 'Note!! '.$note.'<br/>';
}

function errornote($text)
{
	print "<br/><font color=\"red\" style=\"font-weight:bold;\" > Error!!</font> <bold style=\"font-weight:bold;\">$text</bold><br/>";
}

function message($text, $path=NULL)
{
	global $module,$method;

	print "<br/>$text<br/>";
	if($path != 'no')
		print '<a class="information" href="main.php?module='.$module.'&method='.$path.'">Go back to application</a>';
}

function errormess($text, $path=NULL)
{
	global $module;

	if(!$path)
		$path = '';
	print "<br/><font class=\"error\"> Error!!</font> <bold style=\"font-weight:bold;\">$text</bold><br/>";
	if($path != 'no')
		print '<a class="information" href="main.php?module=' .$module.  '&method=' . $path . '">Go back to application</a><br/>';
}

function plainmessage($text)
{
	print "<br/><bold style=\"font-weight:bold;\">$text</bold><br/>";
}

function notify($res)
{
	global $path;

	if($res[0])
		message($res[1],$path);
	else
		errormess($res[1],$path);
}

function escape_page_params()
{
	foreach ($_POST as $param=>$value)
		$_POST[$param] =  escape_page_param($value);
	foreach ($_GET as $param=>$value)
		$_GET[$param] = escape_page_param($value);
	foreach ($_REQUEST as $param=>$value)
		$_REQUEST[$param] = escape_page_param($value);
}

function escape_page_param($value)
{
	if (!is_array($value))
		return htmlentities($value);
	else  {
		foreach ($value as $index=>$val)
			$value[$index] = htmlentities($val);
		return $value;
	}
}

function getparam($param,$escape = true)
{
	$ret = NULL;
	if (isset($_POST[$param]))
		$ret = $_POST[$param];
	else if (isset($_GET[$param]))
		$ret = $_GET[$param];
	else
		return NULL;
	if(is_array($ret)) {
		foreach($ret as $index=>$value) {
			if (substr($ret[$index],0,6) == "__sql_")
				$ret[$index] = NULL; 
			if ($ret[$index] == "__empty")
				$ret[$index] = NULL;
			if ($ret[$index] == "__non_empty" || $ret[$index] == "__not_empty")
				$ret[$index] = NULL;
			if (substr($ret[$index],0,6) == "__LIKE")
				$ret[$index] = NULL;
			if (substr($ret[$index],0,10) == "__NOT LIKE")
				$ret = NULL;
		}
		return $ret;
	}
	if (substr($ret,0,6) == "__sql_")
		$ret = NULL; 
	if ($ret == "__empty")
		$ret = NULL;
	if ($ret == "__non_empty" || $ret == "__not_empty")
		$ret = NULL;
	if (substr($ret,0,6) == "__LIKE")
		$ret = NULL;
	if (substr($ret,0,10) == "__NOT LIKE")
		$ret = NULL;
	return $ret;
}

function killspaces($value)
{
	return str_replace(' ','_',$value);
}

function Numerify($num, $very_big = false)
{
	if ($num == '0') 
		$num = '0';
	if($very_big) {
		for($i=0; $i<strlen($num); $i++) {
			if(!is_numeric($num[$i]))
				return "NULL";
		}
	}else
		if (!is_numeric($num) && strlen($num)) 
			$num = "NULL";
	return $num;
}

// Build a full date string from parts, return false on failure, true on empty
function dateCheck($year,$month,$day,$hour,$end)
{
	if ("$year$month$day" == "") {
		if ($hour == "")
	    	return true;
		if (($hour<0) || ($hour>23))
	    	return false;
		$hour=sprintf(" %02u:%02u:%02u",$hour,$end,$end);
		return date("Y-m-d") . $hour;
    }
	if (!($year && $month && $day))
		return false;
	if ($hour == "")
		$hour=$end ? 23 : 0;
	if (!(is_numeric($year) && is_numeric($month) && is_numeric($day) && is_numeric($hour)))
		return false;
	if (($year<2000) || ($month<1) || ($month>12) || ($day<1) || ($day>31) || ($hour<0) || ($hour>23))
		return false;
	return sprintf("%04u-%02u-%02u %02u:%02u:%02u",$year,$month,$day,$hour,$end,$end);
}

function items_on_page($nrs = array(20,50,100))
{
	global $module, $method;

	$link = $_SESSION["main"] ? $_SESSION["main"] : "main.php";
	$link .= "?";	
	foreach($_REQUEST as $param=>$value)
	{
		if($param == "page" || $param == "PHPSESSID")
			continue;
		$link .= "&$param=$value";
	}

	if(substr($link,-1) != "?")
		$link .= "&";
	$link .= "module=$module&method=$method";

	for($i=0; $i<count($nrs); $i++)
	{
		$option = $link."&limit=".$nrs[$i];
		if ($i>0)
			print '|';
		print '&nbsp;<a class="pagelink" href="'.$option.'">'.$nrs[$i].'</a>&nbsp;';
	}
}

function pages($total = NULL, $params = array())
{
	global $limit, $page, $module, $method, $action;
	if(!$limit)
		$limit = 20;

	$link = $_SESSION["main"] ? $_SESSION["main"] : "main.php";
	$link .= "?";
	$slink = $link;
	$found_total = false;
	$page = 0;
	foreach($_REQUEST as $param=>$value)
	{
		if($param == "action")
			continue;
		if($link != $slink)
			$link .= "&";
		$link .= "$param=$value";
		if($param == "total")
		{
			$total = $value;
			$found_total = true;
		}elseif($param == "page")
			$page = $value;
	}

	if(!$total)
		$total = 0;
	if($total < $limit)
		return;

	if(!$found_total)
		$link .= "&total=$total";

	if(substr($link, -1) != "?")
		$link .= "&";
	$link .= "module=$module&method=$method&action=$action";

	$pages = floor($total/$limit);
	print '<center>';
	if($page != 0)
	{
		$prev_page = $page - $limit;
		print '<a class="pagelink" href="'.$link.'&page='.$prev_page.'"><<</a>&nbsp;&nbsp;';
		$diff = floor(($total - ($page + $limit * 2))/$limit) * $limit;
		$sp = $page - $limit * 2;
		if($diff < 0){
			$sp = $sp - abs($diff);
		}

		while($sp<0)
			$sp += $limit;
		while($sp<$page)
		{
			$pg_nr = $sp/$limit + 1;
			print '<a class="pagelink" href="'.$link.'&page='.$sp.'">'.$pg_nr.'</a>&nbsp;&nbsp;';
			$sp += $limit;
		}
	}
	$pg_nr = $page/$limit + 1;
	print '<font class="pagelink" href="#">'.$pg_nr.'</font>&nbsp;&nbsp;';
	if(($page+$limit)<=$total)
	{
		if($pg_nr>=3)
			$stop_at = $pg_nr + 2;
		else
			$stop_at = $pg_nr + 5 - (floor($page/$limit)+1);

		$next_page = $page + $limit;
		while($next_page<$total && $pg_nr<$stop_at)
		{
			$pg_nr++;
			print '<a class="pagelink" href="'.$link.'&page='.$next_page.'">'.$pg_nr.'</a>&nbsp;&nbsp;';
			$next_page += $limit;
		}

		$next_page = $page + $limit;

		if($next_page<$total)
			print '<a class="pagelink" href="'.$link.'&page='.$next_page.'">>></a>&nbsp;&nbsp;';
	}

	print '</center>';
}

function navbuttons($params=array(),$class = "llink")
{
	global $module, $method, $page;

	$step = '';
	$link="main.php?module=$module&method=$method&";
	foreach($params as $key => $value)
	{
		if ($key=="page" || $key=="tot")
			continue;
		$link="$link$key=$value&";
		if ($key == "step")
			$step = $value;
	}
	$total = $params["tot"];
	
	if(!$step || $step == '')
		$step = 10;
?>
	<center>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td class="navbuttons">
				<?php $vl=$page-$step;
				if ($vl>=0)
				{ ?>
					<font size="-1"><a class="<?php print $class;?>" href="<?php print ("$link"."page"."=$vl");?>">Previous</a>&nbsp;&nbsp;</font>
				<?php } ?>
			</td>
			<td class="navbuttons">
				<font size="-3">
				<?php
				$r=$page/$step+1;
 				print ("$r");
				?>
				</font>
			</td>
			<td class="navbuttons">
			    <?php
			    $vl=$page+$step;
			    if ($vl<$total) { ?>
				&nbsp;&nbsp;<font size="-1"><a class="<?php print $class;?>" href="<?php print ("$link"."page"."=$vl");?>">Next</a> </font><?php
				} ?>
			</td>
		</tr>
	</table>
	</center>
<?php
}

function check_valid_mail($mail)
{
	if(!$mail)
		return true;

	$pattern = '^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$' ;
	return eregi($pattern, $mail);
}

function addHidden($action=NULL, $additional = array())
{
	global $method,$module;
	print "<input type=\"hidden\" name=\"method\" value=\"$method\" />\n";
	if(is_array($module))
		print "<input type=\"hidden\" name=\"module\" value=\"$module[0]\" />\n";
	else
		print "<input type=\"hidden\" name=\"module\" value=\"$module\" />\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$action\" />\n";
	if(count($additional))
		foreach($additional as $key=>$value) 
			print '<input type="hidden" name="' . $key . '" value="' . $value . '">';
}

/**
 * Creates a form for editing an object
 * @param $object Object that will be edited or NULL if fields don't belong to an object
 * @param $fields Array of type field_name=>field_formats
 * Ex: $fields =  array("username"=>array("display"=>"fixed", "compulsory"=>true), 
						// if index 0 in the array is not set then this field will correspond to variable username of @ref $object
						// the field will be marked with a *(compulsory)
						"description"=>array("display"=>"textarea", "comment"=>"short description"), 
						// "comment" is used for inserting a comment under the html element 
						"password"=>array("display"=>"password", "compulsory"=>"yes"), 
						"birthday"=>array("date", "display"=>"include_date"), 
						// will call function include_date
						"category"=>array($categories, "display"=>"select") 
						// $categories is an array like 
						// $categories = array(array("category_id"=>"4", "category"=>"Nature"), array("category_id"=>"5", "category"=>"Movies")); when select category 'Nature' $_POST["category"] will be 4
						// or $categories = array("Nature", "Movies");
						"sex"=>array($sex, "display"=>"radio") 
						// $sex = array("male","female","don't want to answer");
				); 
 * instead of "compulsory", "requited" can be also used
 * possible values for "display" are "textarea", "password", "fileselect", "text", "select", "radio", "radios", "checkbox", "fixed"
 * If not specified display is "text"
 * If the field corresponds to a bool field in the object given display is ignored and display is set to "checkbox"
 * @param $title Text representing the title of the form
 * @param $submit Text representing the value of the submit button or Array of values that will appear as more submit buttons
 * @param $compulsory_notice Bool true for using default notice, Text representing a notice that will be printed under the form if other notice is desired or NULL or false for no notice
 * @param $no_reset When set to true the reset button won't be displayed, Default value is false
 * @param $css Name of the css to be used when generating the elements. Default value is 'edit'
 * @param $form_identifier Text. Used to make the current fields unique(Used when this function is called more than once inside the same form with fields that can have the same name when being displayed)
 * @param $td_width Array or by default NULL. If Array("left"=>$value_left, "right"=>$value_right), force the widths to the ones provided. $value_left could be 20px or 20%.
 * @param $hide_advanced Bool default false. When true advanced fields will be always hidden when displaying form
 */
function editObject($object, $fields, $title, $submit="Submit", $compulsory_notice=NULL, $no_reset=false, $css=NULL, $form_identifier='', $td_width=NULL, $hide_advanced=false)
{
	if(!$css)
		$css = "edit";
	print '<center>';
	print '<table class="'.$css.'" cellspacing="0" cellpadding="0">';
	if($title) {
		print '<tr class="'.$css.'">';
		print '<th class="'.$css.'" colspan="2">'.$title.'</th>';
		print '</tr>';
	}

	$show_advanced = false;
	$have_advanced = false;
	//find if there are any fields marked as advanced that have a value(if so then all advanced fields should be displayed)
	foreach($fields as $field_name=>$field_format)
	{
		if(!isset($field_format["advanced"]))
			continue;
		if($field_format["advanced"] != true)
			continue;
		$have_advanced = true;
		if($object)
			$value = (!is_array($field_name) && isset($object->{$field_name})) ? $object->{$field_name} : NULL;
		else
			$value = NULL;
		if(isset($field_format["value"]))
			$value = $field_format["value"];

		$variable = $object->variable($field_name);
		if((!$variable && $value && !$hide_advanced))
		{
			$show_advanced = true;
			break;
		}
		if(!$variable)
			continue;
		if (($value && $variable->_type != "bool" && !$hide_advanced) || ($variable->_type == "bool" && $value == "t" && !$hide_advanced))
		{
			$show_advanced = true;
			break;
		}
	}
	foreach($fields as $field_name=>$field_format)
		display_pair($field_name, $field_format, $object, $form_identifier, $css, $show_advanced, $td_width);

	if($have_advanced && !$compulsory_notice)
	{
		print '<tr class="'.$css.'">';
		print '<td class="'.$css.' left_td advanced">&nbsp;</th>';
		print '<td class="'.$css.' left_right advanced"><img id="'.$form_identifier.'advanced"';
		if(!$show_advanced)
			print " src=\"images/advanced.jpg\" title=\"Show advanced fields\"";
		else
			print " src=\"images/basic.jpg\" title=\"Hide advanced fields\"";
		print ' onClick="advanced(\''.$form_identifier.'\');"/></th></tr>';
	}
	if($compulsory_notice && $compulsory_notice !== true)
	{
		if($have_advanced) {
		print '<tr class="'.$css.'">';
		print '<td class="'.$css.' left_td" colspan="2">';
		print '<img class="advanced" id="'.$form_identifier.'advanced" ';
		if(!$show_advanced)
			print "src=\"images/advanced.jpg\" title=\"Show advanced fields\"";
		else
			print "src=\"images/basic.jpg\" title=\"Hide advanced fields\"";
		print ' onClick="advanced(\''.$form_identifier.'\');"/>'.$compulsory_notice.'</td>';
		print '</tr>';
		}
	}elseif($compulsory_notice === true){
		print '<tr class="'.$css.'">';
		print '<td class="'.$css.' left_td" colspan="2">';
		if($have_advanced) {
		print '<img id="'.$form_identifier.'advanced"';
		if(!$show_advanced)
			print " class=\"advanced\" src=\"images/advanced.jpg\" title=\"Show advanced fields\"";
		else
			print " class=\"advanced\" src=\"images/basic.jpg\" title=\"Hide advanced fields\"";
		print ' onClick="advanced(\''.$form_identifier.'\');"/>';
		}
		print 'Fields marked with <font class="compulsory">*</font> are required.</td>';
		print '</tr>';
	}
	if($submit != "no" && $submit != "no_submit")
	{
		print '<tr class="'.$css.'">';
		print '<td class="'.$css.' trailer" colspan="2">';
		if(is_array($submit))
		{
			for($i=0; $i<count($submit); $i++)
			{
				print '&nbsp;&nbsp;';
				print '<input class="'.$css.'" type="submit" name="'.$submit[$i].'" value="'.$submit[$i].'"/>';
			}
		}else
			print '<input class="'.$css.'" type="submit" name="'.$submit.'" value="'.$submit.'"/>';
		if(!$no_reset)
			print '&nbsp;&nbsp;<input class="'.$css.'" type="reset" value="Reset"/>';
		print '</td>';
		print '</tr>';
	}
	print '</table>';
	print '</center>';
}

function display_pair($field_name, $field_format, $object, $form_identifier, $css, $show_advanced, $td_width)
{
		if(isset($field_format["advanced"]))
			$have_advanced = true;

		if(isset($field_format["triggered_by"]))
			$needs_trigger = true;

		if($object)
			$value = (!is_array($field_name) && isset($object->{$field_name})) ? $object->{$field_name} : NULL;
		else
			$value = NULL;
		if(isset($field_format["value"]))
			$value = $field_format["value"];

		print '<tr id="tr_'.$form_identifier.$field_name.'"';
//		if($needs_trigger == true)	
//			print 'name="'.$form_identifier.$field_name.'triggered'.$field_format["triggered_by"].'"';
		print ' class="'.$css.'"';
		if(isset($field_format["advanced"]))
		{
			if(!$show_advanced)
				print ' style="display:none;"';
			elseif(isset($field_format["triggered_by"])){
				if($needs_trigger)
					print ' style="display:none;"';
				else
					print ' style="display:table-row;"';
			}else
				print ' style="display:table-row;"';
		}elseif(isset($field_format["triggered_by"])){
			if($needs_trigger)
				print ' style="display:none;"';
			else
				print ' style="display:table-row;"';
		}
		print '>';
		// if $var_name is an array we won't use it
		$var_name = (isset($field_format[0])) ? $field_format[0] : $field_name;
		$display = (isset($field_format["display"])) ? $field_format["display"] : "text";

		if($object)
		{
			$variable = (!is_array($var_name)) ? $object->variable($var_name) : NULL;
			if($variable)
				if($variable->_type == "bool")
					$display = "checkbox";
		}

		if($display == "message") {
			print '<td class="'.$css.' double_column" colspan="2">';
			print $value;
			print '</td>';
			print '</tr>';
			return;
		}

		if($display != "hidden") {
			print '<td class="'.$css.' left_td"';
			if(isset($td_width["left"]))
				print ' style="width:'.$td_width["left"].'"';
			print '>';
			if(!isset($field_format["column_name"]))
				print ucfirst(str_replace("_","&nbsp;",$field_name));
			else
				print ucfirst($field_format["column_name"]);
			if(isset($field_format["required"]))
				$field_format["compulsory"] = $field_format["required"];
			if(isset($field_format["compulsory"]))
				if($field_format["compulsory"] === true || $field_format["compulsory"] == "yes" || $field_format["compulsory"] == "t" || $field_format["compulsory"] == "true")
					print '<font class="compulsory">*</font>';
			print '</td>';
			print '<td class="'.$css.' right_td"';
			if(isset($td_width["right"]))
				print ' style="width:'.$td_width["right"].'"';
			print '>';
		}
		switch($display)
		{
			case "textarea":
				print '<textarea class="'.$css.'" name="'.$form_identifier.$field_name.'" cols="20" rows="5">';
				print $value;
				print '</textarea>';
				break;
			case "select":
			case "mul_select":
				print '<select class="'.$css.'" name="'.$form_identifier.$field_name.'" id="'.$form_identifier.$field_name.'" ';
				if(isset($field_format["javascript"]))
					print $field_format["javascript"];
				if($display == "mul_select")
					print ' multiple="multiple" size="5"';
				print '>';
				if($display != "mul_select")
					print '<option value="">Not selected</option>';
				$options = (is_array($var_name)) ? $var_name : array();
				if(isset($options["selected"]))
					$selected = $options["selected"];
				elseif(isset($options["SELECTED"]))
					$selected = $options["SELECTED"];
				else
					$selected = '';
				foreach ($options as $var=>$opt) {
					if ($var === "selected" || $var === "SELECTED")
						continue;
					if(count($opt) == 2) {
						$optval = $field_name.'_id';
						$name = $field_name;
						if ($opt[$optval] === $selected || (is_array($selected) && in_array($opt[$optval],$selected))) {
							print '<option value=\''.$opt[$optval].'\' SELECTED ';
							if($opt[$optval] == "__disabled")
								print ' disabled="disabled"';
							print '>' . $opt[$name] . '</option>';
						} else {
							print '<option value=\''.$opt[$optval].'\'';
							if($opt[$optval] == "__disabled")
								print ' disabled="disabled"';
							print '>' . $opt[$name] . '</option>';
						}
					}else{
						if ($opt == $selected ||  (is_array($selected) && in_array($opt[$optval],$selected)))
							print '<option SELECTED >' . $opt . '</option>';
						else
							print '<option>' . $opt . '</option>';
					}
				}
				print '</select>';
				break;
			case "radios":
			case "radio":
				$options = (is_array($var_name)) ? $var_name : array();
				if(isset($options["selected"]))
					$selected = $options["selected"];
				elseif(isset($options["SELECTED"]))
					$selected = $options["SELECTED"];
				else
					$selected = "";
				foreach ($options as $var=>$opt) {
					if ($var === "selected" || $var === "SELECTED")
						continue;
					if(count($opt) == 2) {
						$optval = $field_name.'_id';
						$name = $field_name;
						$value = $opt[$optval];
						$name = $opt[$name];
					}else{
						$value = $opt;
						$name = $opt;
					}
					print '<input class="'.$css.'" type="radio" name="'.$form_identifier.$field_name.'" id="'.$form_identifier.$field_name.'" value=\''.$value.'\'';
					if ($value == $selected)
						print ' CHECKED ';
					if(isset($field_format["javascript"]))
						print $field_format["javascript"];
					print '>' . $name . '&nbsp;&nbsp;';
				}
				break;
			case "checkbox":
				print '<input class="'.$css.'" type="checkbox" name="'.$form_identifier.$field_name.'" id="'.$form_identifier.$field_name.'"';
				if($value == "t" || $value == "on")
					print " CHECKED";
				print '/>';
				break;
			case "text":
			case "password":
			case "file":
			case "hidden":
			case "text-nonedit":
				print '<input class="'.$css.'" type="'.$display.'" name="'.$form_identifier.$field_name.'" id="'.$form_identifier.$field_name.'"';
				if($display != "file" && $display != "password")
					print ' value="'.$value.'"';
				if(isset($field_format["javascript"]))
					print $field_format["javascript"];
				if($display == "text-nonedit")
					print " readonly=''";
				if(isset($field_format["autocomplete"]))
					print " autocomplete=\"".$field_format["autocomplete"]."\"";
				print '>';
				break;
			case "fixed":
				if(strlen($value))
					print $value;
				else
					print "&nbsp;";
				break;
			default:
				// need to do a callback here
				// it might be that we don't have the name seeted to that the javascript function that display the advanced settings could work 
				if(isset($field_format["advanced"]))
					print '<input type="hidden" name="'.$form_identifier.$field_name.'">';
				$value = $display($value,$form_identifier.$field_name); 
				if($value)
					print $value;
		}
		if($display != "hidden") {
			if(isset($field_format["comment"]))
			{
				$comment = $field_format["comment"];
				print '&nbsp;&nbsp;<img class="pointer" src="images/question.jpg" onClick="show_hide_comment(\''.$form_identifier.$field_name.'\');"/>';
				print '<font class="comment" style="display:none;" id="comment_'.$form_identifier.$field_name.'">'.$comment.'</font>';
			}
			print '</td>';
		}
		print '</tr>';
}

function find_field_value($res, $line, $field)
{
	for($j=0; $j<pg_num_fields($res); $j++) {
		if (pg_field_name($res,$j) == $field)
				return pg_fetch_result($res,$line,$j);
	}

	return NULL;
}

function tree($array, $func = "copacClick",$class="copac",$title=NULL)
{
    global $module,$path;
	$i = 0;
	if (!isset($array[$i]) && count($array))
		while(!isset($array[$i]))
			$i++;
	if(count($array)){
		$num = count($array[$i]);
		$verify = array();
		for ($j=0;$j<$num;$j++)
			$verify[$j]="";
	}
	$level = 0;
	if(!$title)
		$title = $module;

	print '<div class="'.$class.'">';
	print '<div class="titlu">' . ucfirst($title). '</div>';
	print '<ul class="copac">';
	for ($i=0;$i<count($array);$i++) {
		$j = 0;
		foreach($array[$i] as $fld=>$val) {
	    	if ($val == "") {
				$j++;
				break;
			}
			if ($val == $verify[$j]) {
				$j++;
				continue;
			}
	    	for (;$level>$j;$level--)
				print "</ul>";
	    	if ($j > $level) {
				print "\n<ul class=\"copac_${level}\" id=\"copac_ul_${i}_${level}\" style=\"display:none\">";
				$level++;
			}
			$tip = ($j+1 < $num) ? "disc" : "square";
			print "\n<li class=\"copac_${j} \" id=\"copac_li_${i}_${j}\" type=\"${tip}\"><a class=\"copac\" href=\"#\" onClick=\"${func}(${i},${j},'${fld}'); return false\">$val</a></li>";
			$verify[$j]=$val;
			for ($k=$j+1;$k<$num;$k++)
				$verify[$k]="";
			$j++;
		}
	}
	for (;$level>0;$level--)
		print "</ul><br/>";
    print "\n</ul></div>";
}

function in_formats($field, $formats)
{
	foreach($formats as $key=>$value) {
		if (substr_count($key, $field))
			return array("key"=>$key, "value"=>$value);
	}

	return false;
}

function query_to_array($res, $formats=array())
{
	$begginings = array('1_', '2_' ,'3_' , '4_', '5_', '6_', '7_', '8_', '9_', '0_');

    $array = array();
    for($i=0; $i<pg_num_rows($res);$i++) {
        $array[$i] = array();
        for($j=0; $j<pg_num_fields($res); $j++) {
			$nm = pg_field_name($res,$j);
			$value = pg_fetch_result($res,$i,$j);
			if(isset($formats[$nm]) || in_formats($nm, $formats)) {
				$arr = in_formats($nm, $formats);

				$val = $arr["value"];
				$nm = $arr["key"];
				if(in_array(substr($nm,0,2), $begginings))
					$nm = substr($nm,2,strlen($nm));
				$save_nm = $nm;

				if(substr($val,0,9) == "function_") {
						$name = substr($val,9,strlen($val));
						$arr = explode(':',$name);

						if(count($arr)>1)
						{
							$nm = $arr[1];
							$name = $arr[0];
						}

						if(str_replace(',','',$save_nm) == $save_nm){
							$value = call_user_func($name,find_field_value($res,$i,$save_nm));
						}else
						{
							$save_nm = explode(',',$save_nm);
							$params = array();
							for($x=0; $x<count($save_nm); $x++)
								$params[trim($save_nm[$x])] = find_field_value($res, $i, trim($save_nm[$x]));
							$value = call_user_func_array($name,$params);
							$save_nm = implode(":",$save_nm);
						}
				}elseif($val)
					$nm = $val;
			}
            $array[$i][$nm] = $value;
        }
    }
    return $array;
}

/**
 * Creates table of objects
 * @param $objects Array with the objects to be displayed
 * @param $formats Array with columns to be displayed in the table
 * Ex: array("username", "function_truncdate:registered_on"=>"date", "function_unifynames:name"=>"first_name,last_name")
 * will display a table having the column names : Username | Registered on | Name
 * "function_truncdate:registered_on"=>"date" means that on each variable named date 
 * for all objects function truncdate will be called and the result will be printed in the table under column Registered on
 * "function_unifynames:name"=>"first_name,last_name" means that under the Name column, for all objects 
 * function unifynames will be called with 2 parameters" the content of variables first_name and last_name from each object 
 * @param $object_name Name of the object to be displayed. It's important only when the number of objects to be printer is 0
 * @param $object_actions Array of $method=>$method_name, $method will be added in the link and $method_name will be printed
 * Ex: array("&method=edit_user"=>"Edit")
 * @param $general_actions Array of $method=>$method_name that will be printed at the end of the table
 * Ex: array("&method=add_user"=>"Add user")
 * @param $base Text representing the name of the page the links from @ref $object_name and @ref $general_actions will be sent
 * Ex: $base = "main.php"
 * If not sent, i will try to see if $_SESSION["main"] was set and create the link. If $_SESSION["main"] was not set then  
 * "main.php" is the default value 
 * @param $insert_checkboxes Bool value. If true then in front of each row a checkbox will be created. The name attribute
 * for it will be "check_".value of the id of the object printed at that row
 * Note!! This parameter is taken into account only if the objects have an id defined
 * @param $css Name of the css to use for this table. Default value is 'content'
 * @param $conditional_css Array ("css_name"=>$conditions) $css is the to be applied on certain rows in the table if the object corresponding to that row complies to the array of $conditions
 */
function tableOfObjects($objects, $formats, $object_name, $object_actions=array(), $general_actions=array(), $base = NULL, $insert_checkboxes = false, $css = "content", $conditional_css = array())
{
	global $db_true, $db_false, $module;

	if(!$db_true)
		$db_true = "yes";
	if(!$db_false)
		$db_false = "no";

	if(!count($objects))
	{
		$plural = get_plural_form($object_name);
		plainmessage("<table class=\"$css\"><tr><td style=\"text-align:right;\">There aren't any $plural in the database.</td></tr>");
		if(!count($general_actions)){
			print '</table>';
			return;
		}
	}

	if(!$base)
	{
		$main = (isset($_SESSION["main"])) ? $_SESSION["main"] : "main.php";
		$base = "$main?module=$module";
	}

	print '<table class="'.$css.'" cellspacing="0" cellpadding="0">';
	if(count($objects))
	{
		print '<tr class="'.$css.'">';
		$no_columns = 0;
		if($insert_checkboxes)
		{
			print '<th class="'.$css.'">&nbsp;</th>';
			$no_columns++;
		}
		// print the name of the columns + add column for each action on object
		foreach($formats as $column_name => $var_name)
		{
			$exploded = explode(":",$column_name);
			if(count($exploded)>1)
				$name = $exploded[1];
			else{
				$name = $column_name;
				if(substr($column_name, 0, 9) == "function_")
					$name = substr($column_name,9);
				if(is_numeric($column_name))
					$name = $var_name;
			}
			print '<th class="'.$css.'">';
			print str_replace("_","&nbsp;",ucfirst($name));
			print '</th>';
			$no_columns++;
		}
		for($i=0; $i<count($object_actions); $i++)
		{
			print '<th class="'.$css.'">&nbsp;</th>';
			$no_columns++;
		}
		print '</tr>';

		$vars = $objects[0]->extendedVariables();
		$class = get_class($objects[0]);
		$id_name = $objects[0]->getIdName();
	}else
		$no_columns = 2;

	for($i=0; $i<count($objects); $i++)
	{
		$cond_css = '';
		foreach($conditional_css as $css_name=>$conditions)
		{
			$add_css = true;
			foreach($conditions as $column=>$cond_column)
			{
				if($objects[$i]->{$column} != $cond_column)
				{
					$add_css = false;
					break;
				}
			}
			if($add_css)
				$cond_css .= " $css_name ";
		}
		print '<tr class="'.$css.'">';
		if($insert_checkboxes && $id_name)
		{
			print '<td class="'.$css.$cond_css;
			if($i%2 == 0)
				print " evenrow";
			print '">';
			print '<input type="checkbox" name="check_'.$objects[$i]->{$id_name}.'"/>';
			print '</td>';
		}
		foreach($formats as $column_name=>$var_name)
		{
			print '<td class="'.$css.$cond_css;
			if($i%2 == 0)
				print " evenrow";
			print '">';
			$use_vars = explode(",", $var_name);
			array_walk($use_vars, 'trim_value');
			$exploded_col = explode(":", $column_name);
			$column_value = '';

			if(substr($exploded_col[0],0,9) == "function_") 
			{
				$function_name = substr($exploded_col[0],9,strlen($exploded_col[0]));
				if(count($use_vars)) 
				{
					$params = array();
					for($var_nr=0; $var_nr<count($use_vars); $var_nr++)
						if(array_key_exists($use_vars[$var_nr], $vars))
							array_push($params, $objects[$i]->{$use_vars[$var_nr]});
					$column_value = call_user_func_array($function_name,$params);
				}
			}elseif(isset($objects[$i]->{$var_name})){
				$column_value = $objects[$i]->{$var_name};
				$var = $objects[$i]->variable($use_vars[0]);
				if($var->_type == "bool")
				{
					if($column_value == "t")
						$column_value = $db_true;
					else
						$column_value = $db_false;
				}
			}
			if($column_value !== NULL)
				print $column_value;
			else
				print "&nbsp;";
			print '</td>';
		}
		$link = '';
		foreach($vars as $var_name => $var)
			$link .= "&$var_name=".urlencode($objects[$i]->{$var_name});
		$link_no = 0;
		foreach($object_actions as $methd=>$methd_name)
		{
			print '<td class="'.$css.$cond_css;
			if($i%2 == 0)
				print ' evenrow object_action';
			print '">';
			if($link_no)
				print '&nbsp;&nbsp;';
			print '<a class="'.$css.'" href="'.$base.$methd.$link.'">'.$methd_name.'</a>';
			print '</td>';
			$link_no++;
		}
		print '</tr>';
	}
	if(count($general_actions))
	{
		print '<tr>';
		if(isset($general_actions["left"]))
		{
			$left_actions = $general_actions["left"];
			$columns_left = floor($no_columns/2);
			$no_columns -= $columns_left;
			print '<td class="'.$css.' allleft endtable" colspan="'.$columns_left.'">';
			$link_no = 0;
			foreach($left_actions as $methd=>$methd_name)
			{
				if($link_no)
					print '&nbsp;&nbsp;';
				if (is_numeric($methd))
						print $methd_name;
				else
						print '<a class="'.$css.'" href="'.$base.$methd.'">'.$methd_name.'</a>';
				$link_no++;
			}
			print '</td>';
			if(isset($general_actions["right"]))
				$general_actions = $general_actions["right"];
			else
				$general_actions = array();
		}
		
		print '<td class="'.$css.' allright endtable" colspan="'.$no_columns.'">';
		$link_no = 0;
		if(!count($general_actions))
			print "&nbsp;";
		foreach($general_actions as $methd=>$methd_name)
		{
			if($link_no)
					print '&nbsp;&nbsp;';
			if (is_numeric($methd))
					print $methd_name;
			else
					print '<a class="'.$css.'" href="'.$base.$methd.'">'.$methd_name.'</a>';
			$link_no++;
		}
		print '</td>';
		print '</tr>';
	}
	print "</table>";
}

function get_plural_form($object_name)
{
	if(class_exists($object_name)){
		$obj = new $object_name;
		$plural = $obj->getTableName();
	}else{
		if(substr($object_name,-1) == "s")
			$plural = $object_name;
		elseif(substr($object_name, -1) == "y")
			$plural = substr($object_name,0,strlen($object_name)-1)."ies";
		else
			$plural = $object_name."s";
	}
	return $plural;
}

function trim_value(&$value) 
{ 
	$value = trim($value); 
}

function table($array, $formats, $element_name, $id_name, $element_actions =array(), $general_actions=array(), $base = NULL, $insert_checkboxes = false, $css = "content", $conditional_css = array())
{
	global $module;

	if(!$css)
		$css = "content";
	if(!count($array))
	{
		$plural = get_plural_form($element_name);
		plainmessage("<table class=\"$css\"><tr><td>There aren't any $plural in the database.</td></tr>");
		if(!count($general_actions)){
			print '</table>';
			return;
		}
	}

	if(!$base)
	{
		$main = (isset($_SESSION["main"])) ? $_SESSION["main"] : "main.php";
		$base = "$main?module=$module";
	}

	$lines = count($array);
	if ($element_actions)
		$act = count($element_actions);
	else
		$act = NULL;

	print '<table class="'.$css.'" cellspacing="0" cellpadding="0">';
	if($lines) 
	{
		print '<tr class="'.$css.'">';
		$no_columns = 0;
		if($insert_checkboxes)
		{
			print '<th class="'.$css.'">&nbsp;</th>';
			$no_columns++;
		}
		// print the name of the columns + add column for each action on object

		foreach($formats as $column_name => $var_name)
		{
			$exploded = explode(":",$column_name);
			if(count($exploded)>1)
				$name = $exploded[1];
			else{
				$name = $column_name;
				if(substr($column_name, 0, 9) == "function_")
					$name = substr($column_name,9);
				if(is_numeric($column_name))
					$name = $var_name;
			}
			print '<th class="'.$css.'">';
			print str_replace("_","&nbsp;",ucfirst($name));
			print '</th>';
			$no_columns++;
		}
		for($i=0; $i<count($element_actions); $i++)
		{
			print '<th class="'.$css.'">&nbsp;</th>';
			$no_columns++;
		}
		print '</tr>';
	}else
		$no_columns = 2;

	for($i=0; $i<count($array); $i++) 
	{
		$cond_css = '';
		foreach($conditional_css as $css_name=>$conditions)
		{
			$add_css = true;
			foreach($conditions as $column=>$cond_column)
			{
				if($array[$i][$column] != $cond_column)
				{
					$add_css = false;
					break;
				}
			}
			if($add_css)
				$cond_css .= " $css_name ";
		}
		print '<tr class="'.$css.'">';
		if($insert_checkboxes && $id_name)
		{
			print '<td class="'.$css. "$cond_css";
			if($i%2 == 0)
				print " evenrow";
			print '">';
			print '<input type="checkbox" name="check_'.$array[$i][$id_name].'"/>';
			print '</td>';
		}
		foreach($formats as $column_name=>$names_in_array)
		{
			print '<td class="'.$css. "$cond_css";
			if($i%2 == 0)
				print " evenrow";

			print '">';
			$use_vars = explode(",", $names_in_array);
			$exploded_col = explode(":", $column_name);
			$column_value = '';

			if(substr($exploded_col[0],0,9) == "function_") 
			{
				$function_name = substr($exploded_col[0],9,strlen($exploded_col[0]));
				if(count($use_vars)) 
				{
					$params = array();
					for($var_nr=0; $var_nr<count($use_vars); $var_nr++)
						array_push($params, $array[$i][$use_vars[$var_nr]]);
					$column_value = call_user_func_array($function_name,$params);
				}
			}elseif(isset($array[$i][$names_in_array])){
				$column_value = $array[$i][$names_in_array];
			}
			if($column_value)
				print $column_value;
			else
				print "&nbsp;";
			print '</td>';
		}
		$link = '';
		foreach($array[$i] as $col_name => $col_value)
			$link .= "&$col_name=".urlencode($col_value);
		$link_no = 0;
		foreach($element_actions as $methd=>$methd_name)
		{
			print '<td class="'.$css. "$cond_css";
			if($i%2 == 0)
				print ' evenrow object_action';
			print '">';
			if($link_no)
				print '&nbsp;&nbsp;';
			print '<a class="'.$css. "$cond_css".'" href="'.$base.$methd.$link.'">'.$methd_name.'</a>';
			print '</td>';
			$link_no++;
		}
		print '</tr>';
	}

	if(count($general_actions))
	{
		print '<tr>';
		if(isset($general_actions["left"]))
		{
			$left_actions = $general_actions["left"];
			$columns_left = floor($no_columns/2);
			$no_columns -= $columns_left;
			print '<td class="'.$css.' allleft endtable" colspan="'.$columns_left.'">';
			$link_no = 0;
			foreach($left_actions as $methd=>$methd_name)
			{
				if($link_no)
					print '&nbsp;&nbsp;';
				print '<a class="'.$css.'" href="'.$base.$methd.'">'.$methd_name.'</a>';
				$link_no++;
			}
			print '</td>';
			if(isset($general_actions["right"]))
				$general_actions = $general_actions["right"];
			else
				$general_actions = array();
		}
		
		print '<td class="'.$css.' allright endtable" colspan="'.$no_columns.'">';
		$link_no = 0;
		if(!count($general_actions))
			print "&nbsp;";
		foreach($general_actions as $methd=>$methd_name)
		{
			if($link_no)
				print '&nbsp;&nbsp;';
			print '<a class="'.$css.'" href="'.$base.$methd.'">'.$methd_name.'</a>';
			$link_no++;
		}
		print '</td>';
		print '</tr>';
	}
	print "</table>";
}

function ack_delete($object, $value=NULL, $message=NULL, $object_id=NULL, $value_id=NULL, $additional=NULL)
{
	global $module, $method;

	if(!$object_id)
		$object_id = $object.'_id';
	if(!$value_id)
		$value_id = getparam($object_id);

	print "<br/><br/>Are you sure you want to delete ".str_replace("_","&nbsp;",$object)." $value?";
	if ($message) {
		if(substr($message,0,1) == ",")
			$message = substr($message, 1, strlen($message));
		print " If you delete it you will also delete or set to NULL it's associated objects from $message.";
	}

	print "<br/><br/>";

	print '<a class="llink" href="main.php?module=' . $module . '&method=' . $method . '&action=database&' . $object_id . '=' . $value_id . $additional . '">Yes</a>';

	print '&nbsp;&nbsp;&nbsp;&nbsp;'; 

	print '<a class="llink" href="main.php?module='.$module.'">No</a>';
}

function month_year($value=NULL,$identifier=NULL)
{
	if($value) {
		$value = explode(" ",$value);
		$month = $value[0];
		$years = $value[1];
	}else{
		$month = $years = NULL;
	}
	$year = date('Y');
	?>
	<select name="<?php print $identifier;?>month">
		<option <?php if($month=="January" || $month=="1") print("SELECTED");?>> January </option>
		<option <?php if($month=="February" || $month=="2") print("SELECTED");?>> February </option>
		<option <?php if($month=="March" || $month=="3") print("SELECTED");?>> March </option>
		<option <?php if($month=="April" || $month=="4") print("SELECTED");?>> April </option>
		<option <?php if($month=="May" || $month=="5") print("SELECTED");?>> May </option>
		<option <?php if($month=="June" || $month=="6") print("SELECTED");?>> June </option>
		<option <?php if($month=="July" || $month=="7") print("SELECTED");?>> July </option>
		<option <?php if($month=="August" || $month=="8") print("SELECTED");?>> August </option>
		<option <?php if($month=="September" || $month=="9") print("SELECTED");?>> September </option>
		<option <?php if($month=="October" || $month=="10") print("SELECTED");?>> October </option>
		<option <?php if($month=="November" || $month=="11") print("SELECTED");?>> November </option>
		<option <?php if($month=="December" || $month=="12") print("SELECTED");?>> December </option>
	</select><select name="<?php print $identifier;?>year">
		<?php
		for($i=$year; $i<($year+15);$i++) {
			?><option <?php if ($years == $i) print 'SELECTED';?>><?php print $i; ?></option><?php
		}
		?>
	</select>
	<?php		
}

function month_day_year($date,$key='')
{
	if(!$date || $date == '') {
		$today = date('F-j-Y-H-i',time());
		$today = explode("-",$today); 
		$month = $today[0]; 
		$day = $today[1]; 
		$year = $today[2];
		$hour = $today[3];
		$min = $today[4];
	}else{
		$today = $date;
		$today = explode(" ",$today);
		$today = $today[0];
		$today = explode("-",$today); 
		$month = $today[1]; 
		$day = $today[2]; 
		$year = $today[0];
	}

	?>
	<select name="<?php print $key;?>month">
		<option <?php if($month=="January" || $month=="1" || $month == "01") print("SELECTED");?>> January </option>
		<option <?php if($month=="February" || $month=="2" || $month == '02') print("SELECTED");?>> February </option>
		<option <?php if($month=="March" || $month=="3" || $month == '03') print("SELECTED");?>> March </option>
		<option <?php if($month=="April" || $month=="4" || $month == '04') print("SELECTED");?>> April </option>
		<option <?php if($month=="May" || $month=="5" || $month == '05') print("SELECTED");?>> May </option>
		<option <?php if($month=="June" || $month=="6" || $month == '06') print("SELECTED");?>> June </option>
		<option <?php if($month=="July" || $month=="7" || $month == '07') print("SELECTED");?>> July </option>
		<option <?php if($month=="August" || $month=="8" || $month == '08') print("SELECTED");?>> August </option>
		<option <?php if($month=="September" || $month=="9" || $month == '09') print("SELECTED");?>> September </option>
		<option <?php if($month=="October" || $month=="10") print("SELECTED");?>> October </option>
		<option <?php if($month=="November" || $month=="11") print("SELECTED");?>> November </option>
		<option <?php if($month=="December" || $month=="12") print("SELECTED");?>> December </option>
	</select><select name="<?php print $key;?>day">
		<?php for ($i=1;$i<32;$i++) { ?>
			<option <?php if($day==$i) print("SELECTED");?>> <?php print $i;?></option>
		<?php } ?>
	</select>
	<input type="text" name="<?php print $key;?>year" value="<?php print $year;?>" size="4"/>
	<?php
}

function month_day_year_hour_end($val, $key = NULL)
{
	month_day_year_hour($val,$key,false);
}

function month_day_year_hour($val, $key = NULL, $begin=true)
{
	$year = getparam($val.'year');
	$month = getparam($val.'month');
	$day = getparam($val.'day');
	$hour = getparam($val.'hour');
	if (!$year) {
		$today = date('F-j-Y-H-i',time());
		$today = explode("-",$today); 
		$month = $today[0]; 
		$day = $today[1]; 
		$year = $today[2];
		$hour = $today[3];
		$min = $today[4];
		$hour = ($begin) ? 0 : 23;	
	}
		?>
	Month:&nbsp;<select name="<?php print $key;?>month">
		<option <?php if($month=="January" || $month=="1") print("SELECTED");?>> January </option>
		<option <?php if($month=="February" || $month=="2") print("SELECTED");?>> February </option>
		<option <?php if($month=="March" || $month=="3") print("SELECTED");?>> March </option>
		<option <?php if($month=="April" || $month=="4") print("SELECTED");?>> April </option>
		<option <?php if($month=="May" || $month=="5") print("SELECTED");?>> May </option>
		<option <?php if($month=="June" || $month=="6") print("SELECTED");?>> June </option>
		<option <?php if($month=="July" || $month=="7") print("SELECTED");?>> July </option>
		<option <?php if($month=="August" || $month=="8") print("SELECTED");?>> August </option>
		<option <?php if($month=="September" || $month=="9") print("SELECTED");?>> September </option>
		<option <?php if($month=="October" || $month=="10") print("SELECTED");?>> October </option>
		<option <?php if($month=="November" || $month=="11") print("SELECTED");?>> November </option>
		<option <?php if($month=="December" || $month=="12") print("SELECTED");?>> December </option>
	</select>&nbsp; Day:&nbsp;<select name="<?php print $key;?>day">
		<?php for ($i=1;$i<32;$i++) { ?>
			<option <?php if($day==$i) print("SELECTED");?>> <?php print $i;?></option>
		<?php } ?>
	</select>&nbsp;Year:&nbsp;<input type="text" name="<?php print $key;?>year" value="<?php print $year;?>" size="4"/>&nbsp;Hour:&nbsp;<select name="<?php print $key;?>hour">
		<?php for($i=0; $i<=23; $i++) {
				print '<option ';
				if($hour == $i) 
					print 'SELECTED';
				print '>'.$i.'</option>';
			 }
		?>
	</select>
	<?php
}

function seconds_trunc($time)
{
	$date = explode('.',$time);
	$date = $date[0];
	$date = explode(":",$date);
	$sec = count($date) - 1;
	$date[$sec] ++;
	if(strlen($date[$sec]) == 1)
		$date[$sec] = '0'.$date[$sec];
	if($date[$sec] == 60) {
		$date[$sec-1]++;
		$date[$sec] = 0;
	}
	$date = implode(":",$date);
	return $date;
}

function select_time($timestamp)
{
	$date = seconds_trunc($timestamp);
	$date = explode(' ',$date);
	return $date[1];
}

function add_interval($timestamp, $unit, $nr)
{
	$timestamp = explode(" " ,$timestamp);
	$date = explode("-",$timestamp[0]);
	$time = explode(".",$timestamp[1]);
	$time = explode(":",$time[0]);
	$year = $date[0];
	$month = $date[1];
	$day = $date[2];
	$hours = $time[0];
	$minutes = $time[1];
	$seconds = $time[2];

	${$unit} += $nr;
	$date = date('Y-m-d H:i:s',mktime($hours,$minutes,$seconds,$month,$day,$year));

	return $date;
}

function get_time($hour2='00',$min='00',$key='')
{
	$day = getparam($key."day");
	$month = getparam($key."month");
	$month = getmonthnumber($month);
	if(strlen($month))
		$month = '0'.$month;
	$year = getparam($key."year");
	$hour = getparam($key."hour");
	if(!$hour)
		$hour = $hour2;

	if(!checkdate($month,$day,$year)) {
		errormess("This date does not exit : day=".$day.' month='.$month.' year='.$year,'no');
		return;
	}
	$date = mktime($hour,$min,0,$month,$day,$year);
	return $date;
}

function get_date($hour2='00',$min='00',$key='')
{
	$day = getparam($key."day");
	$month = getparam($key."month");
	$month = getmonthnumber($month);
	$year = getparam($key."year");
		$hour = getparam($key."hour");
	if(!$hour)
		$hour = $hour2;

	if(!checkdate($month,$day,$year)) {
		errormess("This date does not exit : day=".$day.' month='.$month.' year='.$year,'no');
		return;
	}
	if(strlen($month) == 1)
		$month = '0'.$month;
	if(strlen($day) == 1)
		$day = '0'.$day;
	$date = "$year-$month-$day $hour:$min:00";
	return $date;
}

function select_date($timestamp)
{
	$timestamp = explode(" ",$timestamp);
	return $timestamp[0];
}

function trunc_date($timestamp)
{
	$timestamp = explode('.',$timestamp);
	return $timestamp[0];
}

function insert_letters($link = "main.php", $force_method = NULL)
{
	global $module,$method;

	if (is_array($module))
		$module = $module[0];
	if ($force_method)
		$method = $force_method;

	$letter = getparam("letter");
	
	if (!$letter)
		$letter = 'A';
	
	$letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "X", "Y", "W", "Z");

	for($i=0; $i<count($letters); $i++) {
		print '&nbsp;';
		if ($letter == $letters[$i])
			print $letter;
		else{
			print '<a class="llink" href="'.$link;
			if(!strpbrk($link,"?"))
				print '?';
			else
				print "&";
			print 'module='.$module.'&method='.$method.'&letter='.$letters[$i].'">'.$letters[$i].'</a>';
		}
	}
	print '<br/><hr>';
	return $letter;
}

function insert_numbers($link = "main.php")
{
	global $module,$method;

	$nr = getparam("nr");
	if(!$nr)
		$nr = 0;

	if (is_array($module))
		$module = $module[0];

	for($i=0; $i<10; $i++) {
		print '&nbsp;';
		if ($nr == $i)
			print $nr;
		else
			print '<a class="llink" href="'.$link.'?module='.$module.'&method='.$method.'&nr='.$i.'">'.$i.'</a>';
	}
	print '<br/><hr>';
	return $nr;
}

function interval_to_minutes($interval)
{
	if(!$interval)
		return NULL;
	$interval2 = explode(':',$interval);

	return round($interval2[0]*60+$interval2[1]+$interval2[2]/60,2);
}

function minutes_to_interval($minutes = NULL)
{
	//minutes should be interger: ingoring seconds
	$minutes = floor($minutes);

	if(!$minutes)
		return '00:00:00';

	$hours = floor($minutes / 60);
	$mins = $minutes - $hours*60;

	//don't care if $hours > 24
	if (strlen($hours) == 1)
		$hours = '0'.$hours;
	if (strlen($mins) == 1)
		$mins = '0'.$mins;
	return "$hours:$mins:00";
}

function gen_random($lim = 8)
{
	$nr = '';
	for ($digit = 0; $digit < $lim; $digit++) {
		$r = rand(0,1);
		$c = ($r==0)? rand(65,90) : rand(97,122);
		$nr .= chr($c);
	}
	return $nr;
}

function getmonthnumber($month)
{
    switch($month){
		case "January":
		case "Ianuarie":
			return '1';
		case "February":
		case "Februarie":
			return '2';
		case "March":
		case "Martie":
			return '3';
		case "April":
		case "Aprilie":
			return '4';
		case "May":
		case "Mai":
			return '5';
		case "June":
		case "Iunie":
			return '6';
		case "July":
		case "Iulie":
			return '7';
		case "August":
			return '8';
		case "September":
		case "Septembrie":
			return '9';
		case "October":
		case "Octombrie":
			return '10';
		case "November":
		case "Noiembrie":
			return '11';
		case "December":
		case "Decembrie":
			return '12';
	}
	return false;
}

function get_month($nr)
{
	switch($nr) {
		case "13":
		case "1":
		case "01":
			return "January";
		case "2":
		case "02":
		case "14":
			return "February";
		case "3":
		case "03":
		case "15":
			return "March";
		case "4":
		case "04":
			return "April";
		case "5":
		case "05":
			return "May";
		case "6":
		case "06":
			return "June";
		case "7":
		case "07":
			return "July";
		case "8":
		case "08":
			return "August";
		case "9":
		case "09":
			return "September";
		case "10":
			return "October";
		case "11":
			return "November";
		case "12":
			return "December";
		default:
			return "Invalid month: $nr";
	}
}

function make_number($value)
{
	$value = str_replace("$",'',$value);
	$value = str_replace(' ','',$value);
	$value = str_replace('%','',$value);
	$value = str_replace('&','',$value);
	return $value;
}

function make_picture($name)
{
	$name = strtolower($name);
	$name = str_replace(" ","_",$name);
	$name .= '.jpg';
	return $name;
}

function next_page($max)
{
	global $module, $method, $limit;
	$offset = getparam("offset");
	if(!$offset)
		$offset = 0;

	$minus = $offset - $limit;
	$plus = $offset + $limit;
	$page = number_format($offset / $limit) + 1;
	print '<br/><center>';
	if($minus >= 0)
		print '<a class="llink" href="main.php?module='.$module.'&method='.$method.'&offset='.$minus.'&max='.$max.'"><<</a>&nbsp;&nbsp;&nbsp;';
	print $page;
	if($plus < $max)
		print '&nbsp;&nbsp;&nbsp<a class="llink" href="main.php?module='.$module.'&method='.$method.'&offset='.$plus.'&max='.$max.'">>></a>&nbsp;&nbsp;&nbsp;';
	print '</center>';
}

function unsetparam($param)
{
	if(isset($_POST[$param]))
		unset($_POST[$param]);
	if(isset($_GET[$param]))
		unset($_GET[$param]);
	if(isset($_REQUEST[$param]))
		unset($_REQUEST[$param]);
}

function bytestostring($size, $precision = 0) 
{
	$sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'kB', 'B');
	$total = count($sizes);

	while($total-- && $size > 1024) 
		$size /= 1024;

	return round($size, $precision).$sizes[$total];
}

function notice($message, $next=NULL, $no_error = true)
{
	global $module;

	if(!$next)
		$next = $module;

	if($no_error)
		print '<div class="notice">'.$message.'</div>';
	else
		print '<div class="notice"><font class="error">Error!! </font>'.$message.'</div>';

	if($next != "no")
		$next();
}

function form_params($fields)
{
	$params = array();
	for($i=0; $i<count($fields); $i++)
		$params[$fields[$i]] = getparam($fields[$i]);
	return $params;
}

function field_value($field, $array)
{
	if(isset($array[$field]))
		return $array[$field];
	return NULL;
}

function explanations($logo, $title, $explanations, $style="explanation")
{
	global $method;

	if(is_array($explanations))
		if(isset($explanations[$method]))
			$text = $explanations[$method];
		else
			$text = $explanations["default"];
	else
		$text = $explanations;

	print '<div class="'.$style.'">';
	print '<table class="fillall" cellspacing="0" cellpadding="0" >';
	print '<tr>';
	print '<td class="logo_wizard" style="padding:5px;">';
	if ($logo && $logo != "")
		print '<img src="'.$logo.'">';
	print '</td>';
	print '<td class="title_wizard" style="padding:5px;" >';
	print '<div class="title_wizard">'.$title.'</div>';
	print '</td>';	
	print '</tr>';
	print '<tr>';
	print '<td class="step_description" style="font-size:13px;padding:5px;" colspan="2">';
	print $text;
	print '</td>';
	print '</tr>';
	print '</table>';
	print '</div>';
}


class ConfFile
{
	public $sections = array();
	public $filename;
	public $structure = array();
	public $chr_comment = array(";","#");
	public $initial_comment = null;

	function __construct($file_name)
	{
		$this->filename = $file_name;
		if(!is_file($this->filename))
			return;
		$file=fopen($this->filename,"r");
		$last_section = "";
		while(!feof($file))
		{
			$row = fgets($file);
			$row = trim($row);
			if(!strlen($row))
				continue;
			if($row == "")
				continue;
			// new section started
			// the second paranthesis is kind of weird but i got both cases
			if(substr($row,0,1) == "[" && substr($row,-1,1)) {
				$last_section = substr($row,1,strlen($row)-2);
				$this->sections[$last_section] = array();
				$this->structure[$last_section] = array();
				continue;
			}
			if(in_array(substr($row,0,1),$this->chr_comment)) {
				if($last_section == "")
					array_push($this->structure, $row);
				else
					array_push($this->structure[$last_section], $row);
				continue;
			}
			// this is not a section (it's part of a section or file does not have sections)
			$params = explode("=", $row, 2);
			if(count($params)>2 || count($params)<2)
				// skip row (wrong format)
				continue;
			if($last_section == ""){
				$this->sections[$params[0]] = trim($params[1]);
				$this->structure[$params[0]] = trim($params[1]);
			}else{
				$this->sections[$last_section][$params[0]] = trim($params[1]);
				$this->structure[$last_section][$params[0]] = trim($params[1]);
			}
		}
		fclose($file);
	}

	function save($write_comments=false)
	{
		$file = fopen($this->filename,"w") or exit("Could not open ".$this->filename." for writing");
		$wrote_something = false;
		if($this->initial_comment)
			fwrite($file, $this->initial_comment."\n");
		foreach($this->structure as $name=>$value)
		{
			// make sure we don't write the initial comment over and over 
			if($this->initial_comment && !$wrote_something && in_array(substr($value,0,1),$this->chr_comment) && $write_comments)
				continue;
			if(!is_array($value)) {
				if(in_array(substr($value,0,1),$this->chr_comment) && is_numeric($name)) {

					//writing a comment
					if ($write_comments)
						fwrite($file, $value."\n");
					continue;
				}
				$wrote_something = true;
				fwrite($file, "$name=".ltrim($value)."\n");
				continue;
			}else
				fwrite($file, "[".$name."]\n");
			$section = $value;
			foreach($section as $param=>$value)
			{
				//writing a comment
				if(in_array(substr($value,0,1),$this->chr_comment) && is_numeric($param)) {
					if ($write_comments)
						fwrite($file, $value."\n");
					continue;
				}
				$wrote_something = true;
				fwrite($file, "$param=".ltrim($value)."\n");
			}
			fwrite($file, "\n");
		}
		fclose($file);
	}
}


function build_dropdown($arr, $name, $show_not_selected = true, $disabled = "", $css="", $javascript="", $just_options=false)
{
	if(!$just_options)
		$res = '<select class="dropdown '.$css.'" name="'.$name.'" id="'.$name.'" '.$disabled.' '.$javascript.'>'."\n";
	else
		$res = '';
	if($show_not_selected)
		$res .= '<option value=""> - </option>'."\n";
	$selected = (isset($arr["selected"]))? $arr["selected"] : "";
	unset($arr["selected"]);
	for($i=0; $i<count($arr); $i++) {
		if(is_array($arr[$i])) {
			$value = $arr[$i]["field_id"];
			$value_name = $arr[$i]["field_name"];
			$res .= "<option value=\"$value\"";
			if($selected == $value)
				$res .= " SELECTED";
			if($value === "__disabled")
				$res .= " disabled=\"disabled\"";
			$res .= ">$value_name</option>\n";
		}
	}
	if(!$just_options)
		$res .= "</select>\n";
	return $res;
}

function format_for_dropdown($vals)
{
	$arr = array();
	for($i=0; $i<count($vals); $i++)
		array_push($arr, array("field_id"=>$vals[$i], "field_name"=>$vals[$i]));
	return $arr;
}

function formTable($rows, $th=null, $title = null, $submit = null, $width=null, $id=null, $css_first_column='')
{
	if(is_array($th))
		$cols = count($th);
	elseif(isset($rows[0]))
		$cols = count($rows[0]);
	else
		$cols = count($rows);
	$width = ($width) ? "style=\"width:".$width."px;\"" : "";
	$id = ($id) ? " id=\"$id\"" : "";
	print '<table class="formtable" cellspacing="0" cellpadding="0" '.$width.' '.$id.'>'."\n";
	if($title) {
		print "<tr>\n";
		print '<th class="title_formtable" colspan="'.$cols.'">'.$title.'</td>'."\n";
		print "</tr>\n";
	}
	if(is_array($th)) {
		print "<tr>\n";
		for($i=0; $i<count($th); $i++) {
			if(is_array($th[$i])) {
				$style = "style=\"width:".$th[$i]["width"].";\"";
				$info = $th[$i][0];
			}else{
				$style = "";
				$info = $th[$i];
			}
			print "<th class=\"formtable\" $style>".$info."</th>\n";
		}
		print "</tr>\n";
	}
	if(isset($rows[0])) {
		for($i=0; $i<count($rows); $i++) {
			$row = $rows[$i];
			print "<tr>\n";
			if(is_array($row)) {
				for($j=0; $j<count($row); $j++) {
					$css = ($i%2 == 0) ? "formtable evenrow" : "formtable";
			//		if($j == 0)
			//			$css .= " $css_first_column"."";
					if($i%2 == 0)
						print "<td class=\"$css\">". $row[$j] ."</td>\n";
					else
						print "<td class=\"$css\">". $row[$j] ."</td>\n";
				}
			}else{
				print '<td class="white_row" colspan="'.count($th).'">'.$row.'</td>';
			}
			print "</tr>\n";
		}
	}else{
		$i = 0;
		foreach($rows as $key=>$format) {
			print "<tr>\n";
			$css = ($i%2 === 0) ? "formtable evenrow" : "formtable oddrow";
			display_pair($key, $format, null, null, $css, null, null);
			print "</tr>\n";
			$i++;
		}
	}
	if($submit) {
		print "<tr>\n";
		print "<td class=\"submit_formtable\" colspan=$cols>";
		print $submit;
		print "</td>";
		print "</tr>\n";
	}
	print "</table>\n";
}

function set_form_fields(&$fields, $error_fields, $field_prefix='')
{
	foreach ($fields as $name=>$def)
	{
		if (!isset($def["display"]))
			$def["display"] = "text";
		if ($def["display"] == "hidden" || $def["display"]=="message" || $def["display"]=="fixed")
			continue;
		if (in_array($name, $error_fields))
			$fields[$name]["error"] = true;
		if (substr($name,-2) == "[]" && $def["display"] == "mul_select")
			$val = getparam($field_prefix.substr($name,0,strlen($name)-2));
		else
			$val = getparam($field_prefix.$name);
		if ($val) {
			if (isset($fields[$name][0]) && is_array($fields[$name][0]))
				$fields[$name][0]["selected"] = $val;
			elseif ($def["display"] == "checkbox")
				$fields[$name]["value"] = ($val == "on") ? "t" : "f";
			else
				$fields[$name]["value"] = $val;
		}
	}
}

function set_error_fields($error, &$error_fields)
{
	// fields between '' are considered names
	$field = '';
	$start = false;
	$error = strtolower($error);
	for	($i=0; $i<strlen($error); $i++) {
		if ($error[$i] != "'") {
			if($start)
				$field .= $error[$i];
		} else {
			if ($start) {
				if(!in_array($field, $error_fields))
					$error_fields[] = $field;
				$field = '';
				$start = false;
			} else
				$start = true;
		}
	}
}

function error_handle($error, &$fields, &$error_fields, $field_prefix='')
{
	if ($error) {
		errormess($error,"no");
		set_error_fields($error, $error_fields);
		set_form_fields($fields, $error_fields, $field_prefix);
	}
}

/* vi: set ts=8 sw=4 sts=4 noet: */
?>
