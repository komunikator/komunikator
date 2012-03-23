<?php
/**
 * menu.php
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
include ("structure.php");

$errorlogin = "Login invalid!";
global $module, $method, $support, $level, $do_not_load, $iframe;

function index_title()
{
	title();
}

function get_login_form()
{
	global $login, $link;
?>
		<link type="text/css" rel="stylesheet" href="index.css"/>
		<div class="login-div">
		<form action="index.php" method="post">
			<fieldset class="login" border="1">
				<legend class="login">Login</legend>
				<?php 
					if ($login) 
						print $login;
					else 
						print "<p>&nbsp;</p>";	
				?>
				<p class="wellcome_to">Welcome to FreeSentral!</p>
				<p align="right"><label id="username">Username:&nbsp;</label><input type="text" name="username" id="username" size="19"/></p>
				<p align="right"><label id="password">Password:&nbsp;</label><input type="password" name="password" id="password" size="19" /></p>
				<p align="right"><input type="submit" value="Send" class="submit"/></p>
				<div align="center">
		<?php
		/*	$sigur = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
		    $s1 = $sigur ? "Cripted SSL" : "Uncripted";
		    $s2 = $sigur ? "deactivate" : "secure";
		    $l = $sigur ? "http://" : "https://";
		    $l .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		    print "<b>$s1</b> <a  class=\"signup\" href=\"$l\">$s2</a>";*/
		?>
				</div>
			</fieldset>
		</form>
		</div>
<?php
}

function title()
{
	global $module;
	print "Tellfon: ".ucwords(str_replace("_"," ",$module));
}

function get_content()
{
	global $module,$dir,$support,$iframe;

	if($iframe != "true") {
	/*?>	<table class="container" cellspacing="0" cellpadding="0">
			<tr>
				<th valign="top">
					<table class="upperbanner"> 
						<tr>
							<td class="upperbanner">
								<div class="upperbanner">Welcome 
								<?php 
									if(isset($_SESSION["real_user"]))
										print $_SESSION["real_user"]. ". You are currently logged in as ";
									print $_SESSION["user"];
								?>!&nbsp;&nbsp;<a class="uplink" href="index.php">Logout</a>&nbsp;&nbsp;
								<?php
									if(isset($_SESSION["real_user"]))
										print '<a class="uplink" href="main.php?method=stop_impersonate">Return&nbsp;to&nbsp;your&nbsp;account</a>';
								?>
								</div></td>
							<td><div class="photo"> <img src="images/logo.jpg"/></td>
						</tr>
					</table>
					<table class="status">
						<tr>
							<td class="status" colspan="2">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</th>
			</tr>
		</table>*/?>
	<table class="container" cellspacing="0" cellpadding="0">
		<tr>
			<td class="holdlogo">
				 <img src="images/logo2.png"/>
			</td>
		</tr>
		<tr>
			<td class="upperbanner">
								<div class="upperbanner">Welcome
								<font class="bluefont">
								<?php 
									if(isset($_SESSION["real_user"]))
										print $_SESSION["real_user"]. ". You are currently logged in as ";
									print $_SESSION["user"];
								?>
								</font>
								!&nbsp;&nbsp;<a class="uplink" href="index.php">Logout</a>&nbsp;&nbsp;
								<?php
									if(isset($_SESSION["real_user"]))
										print '<a class="uplink" href="main.php?method=stop_impersonate">Return&nbsp;to&nbsp;your&nbsp;account</a>';
								?>
								</div>
			</td>
		</tr>
	</table>

	<div class="position"> <br/> </div>
	<table class="firstmenu" cellpadding="0" cellspacing="0">
		<tr>
			<?php menu(); ?>
		</tr>
	</table>
	<?php submenu();
	}
	?>
	<table class="holdcontent" cellspacing="0" cellpadding="0">
		<tr>
			<td class="holdcontent">
	<?php
	$load = ($module == "HOME") ? "home" : $module;
	if($module) {
			if(is_file("modules/$dir/$load.php"))
				include("modules/$dir/$load.php"); 
	} ?>
			</td>
		</tr>
	</table>
<?php
}

function menu()
{
	global $level,$support;
	if ($support)
		files('customer');
	else
    	files($level);
}

function files($level)
{
	global $module;
	$names = array();
	if ($handle = opendir("modules/$level/"))
	{
		while (false !== ($file = readdir($handle)))
		{
			//if ((trim($file,"~") === $file) && (stripos($file,".swp") === false))
			if (substr($file,-4) != ".php")
				continue;
			if (stripos($file,".php") === false)
				continue;
			if($file == "home.php" || $file == "PBX_features.php")
				continue;
			$names[] = ereg_replace('.php','',$file);
		}
		closedir($handle);
	}
	sort($names);
	if(is_file("modules/$level/home.php"))
		$names = array_merge(array("home"), $names);
	if(is_file("modules/$level/PBX_features.php"))
		$names = array_merge($names, array("PBX_features"));
	$i = 0;
	foreach($names as $name)
	{
		if(dont_load($name) || $name == "verify_settings")
			continue;
		if($i)
			print "<td class=\"separator\">&nbsp;</td>";

		if ($name == $module) {
			print "<td class=\"firstmenu_selected\">";
			print '<div  class="linkselected" onclick="location.href=\'main.php?module='.$name.'\'">';
			if($name == "dids")
				$name = "DIDs";
			elseif($name == "home")
				$name = "HOME";
			print str_replace(" ","&nbsp;",ucwords(str_replace("_"," ",$name))).'</div>';
		} else {
			print "<td class=\"firstmenu\">";
			print '<div class="link" onclick="location.href=\'main.php?module='.$name.'\'">';
			if($name == "dids")
				$name = "DIDs";
			elseif($name == "home")
				$name = "HOME";
			print str_replace(" ","&nbsp;",ucwords(str_replace("_"," ",$name))).'</div>';
		}
		print "</td>";
		$i++;
	}
	print("<td class=\"fillspace\">&nbsp;</td>");	
}

function dont_load($name)
{
	global $do_not_load;

	if (!is_array($do_not_load))
		return false;

	for($i=0; $i<count($do_not_load); $i++) {
		if ($do_not_load[$i] == $name)
			return true;
	}

	return false;
}

function submenu()
{
	global $module,$dir,$struct,$method,$support,$block;
	if(!isset($struct[$dir.'_'.$module]))
		return;
	$i = 0;
	$max = 10;
	print '<table class="secondmenu"> 
			<tr>';
	print '<td class="padd">&nbsp;</td>';
	if(!$method) {
		if(in_array("manage", $struct["$dir"."_".$module]))
			$method = "manage";
		elseif(in_array($module, $struct["$dir"."_".$module]))
			$method = $module;
		else
			$method = $struct["$dir"."_".$module][0];
	}

    foreach($struct["$dir"."_".$module] as $option) {
		$res = submenu_check($dir,$module,$option);
		if(!$res)
			continue;
		if($i % $max == 0 && $i){
			print("<td class=\"fillfree\">&nbsp;</td>");
			print '</tr><tr>';
		}
		$printed = false;
		if(isset($block["$dir"."_".$module])) 
			if(in_array($option, $block["$dir"."_".$module])) {
				print("<td class=\"option\"><a class=\"secondlinkinactive\">");
				$printed = true;
			}
		if($method == $option && !$printed)
			print("<td class=\"option\"><a class=\"secondlinkselected\" href=\"main.php?module=$module&method=$option\">");//.strtoupper($option)."</a></td>");
		elseif(!$printed)
			print("<td class=\"option\"><a class=\"secondlink\" href=\"main.php?module=$module&method=$option\">");//.strtoupper($option)."</a></td>");
		print str_replace(" ","&nbsp;",ucwords(str_replace("_"," ",$option)));
		print("</a></td><td class=\"option_separator\"><div></div></td>");
		$i++; 
	}
	print("<td class=\"fillfree\" colspan=\"$max\">&nbsp;</td>");
	print "</tr></table>";
}
/*
function status()
{
    global $dir,$module,$struct,$method,$align;
    if(!(array_key_exists("$dir"."_"."$module",$struct))) {
        print(strtoupper($module));
        return;
    }
    print(ucwords($module).":");
    if(!$method || $method == ''){
        $name = explode("-",($struct["$dir"."_"."$module"][0]));
        if (!$align)
            $align = $name[0];
        $method = $name[1];
    }else{
        $name = $method;
        foreach($struct["$dir"."_"."$module"] as $option) {
            $opt = explode("-",$option);
            if($opt[1] == $method) {
                $align = $opt[0];
                break;
            }
        }
    }
    $num = explode("_",$method);
    for($i=0; $i<count($num); $i++)
        print(ucwords($num[$i])." ");
}*/
?>
