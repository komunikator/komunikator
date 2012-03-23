<?php
/**
 * incoming_gaeways.php
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

class Incoming_gateway extends Model
{
	public static function variables()
	{
		return array(
					"incoming_gateway_id" => new Variable("serial"),
					"incoming_gateway" => new Variable("text", "!null"),
					"gateway_id" => new Variable("serial", "!null", "gateways", true),
					"ip" => new Variable("text", "!null")
				);
	}

	public function setObj($params)
	{
		$this->incoming_gateway = field_value("incoming_gateway", $params);
		if($this->objectExists())
			return array(false, "This incoming gateway was already defined.");

		$copy = new Incoming_gateway;
		$copy->incoming_gateway_id = $this->incoming_gateway_id;
		$copy->ip = field_value("ip", $params);
		$copy->gateway_id = field_value("gateway_id", $params);
		if($copy->objectExists())
			return array(false, "This ip was already define as an incoming gateway for this gateway.");

		if($this->incoming_gateway_id)
			$this->select();
		return parent::setObj($params);
	}

	public function getTableName()
	{
		return "incoming_gateways";
	}
}
?>