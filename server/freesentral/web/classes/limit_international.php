<?php
/**
 * limit_international.php
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

class Limit_international extends Model
{
	public static function variables()
	{
		return array(
			"limit_international_id" => new Variable("serial"),
			"limit_international" => new Variable("text", "!null"),
			"name" => new Variable("text", "!null"),
			"value" => new Variable("text"),
		);
	}

	public function getTableName()
	{
		return "limits_international";
	}

	public static function defaultObject()
	{
		$params = array(
			array("limit_international"=>"Limit 1 minute", "value"=>"3", "name"=>"1minute"),
			array("limit_international"=>"Limit 10 minutes", "value"=>"10", "name"=>"10minutes"),
			array("limit_international"=>"Limit 1 hour", "value"=>"20", "name"=>"1hour")
		);
		$limit = new Limit_international;
		$nr_limit = $limit->fieldSelect("count(*)");
		if ($nr_limit)
			return true;

		foreach($params as $key=>$value) {
			$limit = new Limit_international;
			$limit->limit_international = $value["limit_international"];
			$limit->value = $value["value"];
			$limit->name = $value["name"];
			$limit->insert();
		}
		return true;
	}

	public function setObj($params)
	{
		$this->limit_international = field_value("limit_international", $params);
		if ($this->objectExists())
			return array(false, "This limit is already defined.");
		if ($this->limit_international_id)
			$this->select();
		return parent::setObj($params);
	}
}

?>