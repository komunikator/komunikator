<?php
/**
 * framework.php
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

class Debug
{
	public static function output($msg)
	{
		global $logs_in;

		if(!isset($logs_in))
			$logs_in = "web";

		$arr = $logs_in;
		if(!is_array($arr))
			$arr = array($arr);

		for($i=0; $i<count($arr); $i++) {
			if($arr[$i] == "web") {
				print "<br/>\n<br/>\n$msg<br/>\n<br/>\n";
			}else{
				$date = date("[D M d H:i:s Y]");
				if(!is_file($arr[$i]))
					$fh = fopen($arr[$i], "w");
				else
					$fh = fopen($arr[$i], "a");
				fwrite($fh, $date.' '.$msg."\n");
				fclose($fh);
			}
		}
	}
}
?>