<?php
/**
 * set_debug.php
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

session_start();

if(isset($_SESSION["error_reporting"]))
	ini_set("error_reporting",$_SESSION["error_reporting"]);

if(isset($_SESSION["display_errors"]))
	ini_set("display_errors",true);
else
	ini_set("display_errors",false);

if(isset($_SESSION["log_errors"]))
	ini_set("log_errors",$_SESSION["log_errors"]);
else
	ini_set("log_errors",false);

if(isset($_SESSION["error_log"]))
	ini_set("error_log", $_SESSION["error_log"]);

?>