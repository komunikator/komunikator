<?php
/**
 * prefixes.php
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

class Prefix extends Model
{
	public static function variables()
	{
		return array(
			"prefix_id" => new Variable("serial"),
			"prefix" => new Variable("text", "!null"),
			"name" => new Variable("text"),
			"international" => new Variable("bool", "t")
		);
	}

	public function getTableName()
	{
		return "prefixes";
	}

	public static function defaultObject()
	{
		$params = array(
			array("prefix"=>"00", "name"=>"EU international prefix"),
			array("prefix"=>"011","name"=>"US international prefix"),
			array("prefix"=>"+"  ,"name"=>"prefix for e164 numbers")
		);
		$prefix = new Prefix;
		$nr_prefixes = $prefix->fieldSelect("count(*)");
		if ($nr_prefixes)
			return true;

		foreach($params as $key=>$value) {
			$prefix = new Prefix;
			$prefix->prefix = $value["prefix"];
			$prefix->name = $value["name"];
			$prefix->insert();
		}
		return true;
	}

	public function setObj($params)
	{
		$this->prefix = field_value("prefix", $params);
		if ($this->objectExists())
			return array(false, "This prefix is already defined.");
		if ($this->prefix_id)
			$this->select();
		return parent::setObj($params);
	}
}

?>