<?php
/**
 * index.php
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
require_once("config.php");
require_once("framework.php");
require_once("menu.php");
require_once("lib/lib.php");
require_once("lib/lib_freesentral.php");

include_classes();

require_once("set_debug.php");

//$_SESSION["warning_on"] = true;
//$_SESSION["notice_on"] = true;

if (isset($_SESSION['user']) && isset($_SESSION['level']))
	header ("Location: main.php");

if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"]!="on") {
	$server = $_SERVER["HTTP_HOST"];
	if($server != "localhost" && $server != "127.0.0.1")
		header ("Location: https://".$server.$_SERVER["PHP_SELF"]);
}

$username = getparam("username");
$password = getparam("password");
$login = "";

$user = new User;
$user->username = $username;
$user->password = $password;
if($user->login()){
	$level = 'admin';
}else{
	$extension = new Extension;
	$extension->extension = $username;
	$extension->password = $password;
	if ($extension->login())
		$level = 'extension';
	else
		$level = NULL;
}

if ($level) {
	$_SESSION['user'] = $username;
	$_SESSION['username'] = $username;
	$_SESSION['user_id'] = ($level == "admin") ? $user->user_id : $extension->extension_id;
	$_SESSION['level'] = $level;
	header ("Location: main.php");
}else
	if ($username || $password)
		$login = "<h4>Wrong login</h4>";
	else
		session_unset();
?>
<html>
<title>FreeSentral</title>
<body>
	<?php get_login_form(); ?>
</body>
</html>
