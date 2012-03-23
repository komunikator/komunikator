<?php
/**
 * keys.php
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

class Key extends Model
{
	public static function variables()
	{
		return array(
					"key_id" => new Variable("serial", "!null"),
					"key" => new Variable("text", "!null"),
					"prompt_id" => new Variable("serial", "!null","prompts"),
					"destination" => new Variable("text", "!null"),
					"description" => new Variable("text")
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	// use this function so that name of the table is not automatically "keies"
	public function getTableName()
	{
		return "keys";
	}

	public function setObj($params)
	{
		$this->key = field_value("key", $params);
		if(($msg = $this->objectExists()))
			return array(false, (is_numeric($msg)) ? "This key ".$this->key." is already defined." : $msg);
		if(!is_numeric($this->key)) 
			return array(false, "Field 'Key' must be numeric.");
		if($this->key_id)
			$this->select();
		$this->setParams($params);
		return parent::setObj($params);
	}
}
?>