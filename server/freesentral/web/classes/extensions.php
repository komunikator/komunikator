<?php
/**
 * extensions.php
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
require_once("framework.php");

class Extension extends Model
{
	public static function variables()
	{
		return array(
					"extension_id" => new Variable("serial","!null"),
					"extension" => new Variable("text","!null"),
					"password" => new Variable("text"),
					"firstname" => new Variable("text"),
					"lastname" => new Variable("text"),
					"address" => new Variable("text"),
					"inuse" => new Variable("int4"),
					"location" => new Variable("text"),
					"expires" => new Variable("timestamp"),
					"max_minutes" => new Variable("interval"),
					"used_minutes" => new Variable("interval","00:00:00"),
					"inuse_count" => new Variable("int2"),
					"inuse_last" => new Variable("timestamp"),
					"login_attempts" => new Variable("int2","0")
			/*		"mac_address" => new Variable("text"),
					"equipment_id" => new Variable("serial",NULL,"equipments")*/
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	public function login()
	{
		if (!$this->extension || !$this->password)
			return NULL;
		$thiss = Model::selection("extension", array("extension"=>$this->extension));
		if(count($thiss) == 1) 
		{
			if($thiss[0]->password == $this->password) {
				foreach($thiss[0] as $var_name=>$var)
				{
					$this->{$var_name} = $thiss[0]->{$var_name};
				}
				if($thiss[0]->login_attempts>0){
					$thiss[0]->login_attempts = 0;
					$thiss[0]->fieldUpdate(array("extension_id"=>$thiss[0]->extension_id), array('login_attempts'));
				}
				self::writeLog("extension ".$this->extension." logged in");
				return true;
			} else {
				$thiss[0]->login_attempts++;
				$thiss[0]->fieldUpdate(array("extension_id"=>$thiss[0]->extension_id), array('login_attempts'));
				self::writeLog("failed attempt to log in as extension: ".$this->extension);
				return false;
			}
		} else {
			self::writeLog("failed attempt to log in as unknown extension: ".$this->extension);
			return false;
		}
	}

	public function setObj($params)
	{
		if(($ext = field_value("extension", $params)))
			$this->extension = $ext;
		if(Numerify($this->extension) == "NULL")
			return array(false, "Field extension must be numeric");
		if(strlen($this->extension) < 3)
			return array(false,"Field extension must be minimum 3 digits");

		if(($msg = $this->objectExists()))
			return array(false, (is_numeric($msg)) ? "This extension already exists ".$this->extension : $msg);
		$this->select();
		$this->setParams($params);
		if($this->max_minutes)
			$this->max_minutes = minutes_to_interval($this->max_minutes);
	/*	$this->mac_address = field_value("mac_address",$params);
		if($this->mac_address)
		{
			if(field_value("equipment",$params) != "Not selected")
				$this->equipment_id = field_value("equipment",$params);
			else
				return array(false, "Please select equipment you wish to provision.");
		}*/
		if(!$this->password)
			return array(false,"Field password is compulsory.");
		if(strlen($this->password) < 6)
			return array(false,"Password must be at least 6 digits long");
		if(Numerify($this->password) == "NULL")
			return array(false,"Field password must be numeric");
		return parent::setObj($params);
	}
}