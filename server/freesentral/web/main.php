<?php
/**
 * main.php
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
require_once("set_debug.php");
require_once("config.php");
require_once("lib/lib.php");
require_once("menu.php");
require_once("lib/lib_freesentral.php");

include_classes();

if (!isset($_SESSION['user']) || !isset($_SESSION['level']))
	header ("Location: index.php");


if (isset($_GET['skin']))
    $_SESSION['skin']=$_GET['skin'];

$module = NULL;
$method = NULL;

if(getparam("method") == "impersonate") {
	if($_SESSION["level"] != "admin")
		forbidden();
	Model::writeLog("impersonate ".getparam("extension"));
	$_SESSION["real_user"] = $_SESSION["user"];
	$_SESSION["real_user_id"] = $_SESSION["user_id"];
	$_SESSION["user"] = getparam("extension");
	$_SESSION["username"] = $_SESSION["user"];
	$ext = Model::selection("extension", array("extension"=>getparam("extension")));
	if(!count($ext))
		forbidden();
	$_SESSION["user_id"] = $ext[0]->extension_id;
	$_SESSION["level"] = "extension";
	$module = "home";
	$method = "home";
}elseif(getparam("method") == "stop_impersonate"){
	if(!isset($_SESSION["real_user"])) 
		forbidden();
	$user = new User;
	$user->select(array("username"=>$_SESSION["real_user"]));
	if(!$user->user_id)
		forbidden();
	$impersonated = $_SESSION["user"];
	$_SESSION["user"] = $_SESSION["real_user"];
	$_SESSION["username"] = $_SESSION["real_user"];
	$_SESSION["user_id"] = $_SESSION["real_user_id"];
	Model::writeLog("stop impersonate ".$impersonated);
	$_SESSION["level"] = "admin";
	if(isset($_POST["method"]))
	$_POST["method"] = NULL;
	if(isset($_GET["method"]))
	$_GET["method"] = NULL;
	unset($_SESSION["real_user"]);
	$module = "home";
	$method = "home";
}

$dir = $_SESSION['level'];
$level = $_SESSION['level'];
testpath($dir);

$module = (!$module) ? getparam("module") : $module;
if(!$module)
	if(is_file("modules/$dir/home.php"))
		$module = "home";
testpath($module);

$path = $module;

$action = getparam("action");
$method = (!$method) ? getparam("method") : $method;

$page = getparam("page");
if(!$page)
	$page = 0;

$_SESSION["limit"] = (isset($_SESSION["limit"])) ? $_SESSION["limit"] : 20;
$limit = getparam("limit") ? getparam("limit") : $_SESSION["limit"];
$_SESSION["limit"] = $limit;

if($method == "manage")
	$method = $module;

$_SESSION["main"] = "main.php";
$iframe = getparam("iframe");

// check to see if there are PRI/BRI cards
if(!isset($_SESSION["pri_support"]) || !isset($_SESSION["bri_support"])) {
	include("lib/telephony_cards.php");
	$out = shell_command("server_hwprobe");
	$res = verify_wanrouter_output($out);
	if($res) {
		$spans = get_spans($out);
		for($i=0; $i<count($spans); $i++) {
			$card = $telephony_cards[$spans[$i]["telephony_card"]];
			if($card["type"] == "BRI")
				$_SESSION["bri_support"] = "yes";
			elseif($card["type"] == "PRI")
				$_SESSION["pri_support"] = "yes";
		}
	}
	if(!isset($_SESSION["pri_support"]))
		$_SESSION["pri_support"] = "no";
	if(!isset($_SESSION["bri_support"]))
		$_SESSION["bri_support"] = "no";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>FreeSentral</title>
<?php    include "javascript.php"; ?>
<link type="text/css" rel="stylesheet" href="main.css"/>
<link type="text/css" rel="stylesheet" href="wizard.css"/>
</head>
<!-- <body style="margin: 0 0 0 0;" background="images/sigla.png" bgproperties="fixed"> -->
<body class="mainbody">
	<?php get_content();?>
</body>
</html>
