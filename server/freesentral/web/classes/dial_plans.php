<?php
/**
 * dial_plans.php
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

class Dial_Plan extends Model
{
	public static function variables()
	{
		return array(
					"dial_plan_id" => new Variable("serial", "!null"),
					"dial_plan" => new Variable("text", "!null"),
					"priority" => new Variable("int2", "!null"),
					"prefix" => new Variable("text"),
					// if this dial_plan points to a gateway the next group of fields will be empty
					"gateway_id" => new Variable("serial","!null","gateways",true),
					// if this is not a dial_plan that points to a gateway then some of the fields from below are compulsory
/*					"protocol" => new Variable("text"),
					"ip" => new Variable("text"),
					"port" => new Variable("text"),
					"iaxuser" => new Variable("text"),
					"iaxcontext" => new Variable("text"),
					"chans_group" => new Variable("text"),
					"formats" => new Variable("text"),
					"rtp_forward" => new Variable("bool"),*/
					// optional fields for rewriting digits
					"nr_of_digits_to_cut" => new Variable("int2"),
					"position_to_start_cutting" => new Variable("int2"),
					"nr_of_digits_to_replace" => new Variable("int2"),
					"digits_to_replace_with" => new Variable("text"),
					"position_to_start_replacing" => new Variable("int2"),
					"position_to_start_adding" => new Variable("int2"),
					"digits_to_add" => new Variable("text")
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	function setObj($params)
	{
		$this->dial_plan = field_value("dial_plan", $params);
		if(($msg = $this->objectExists()))
			return array(false, (is_numeric($msg)) ? "This 'Dial plan' is already defined." : $msg, "another try");
		$this->select();
		$this->setParams($params);
		if(Numerify($this->priority) == "NULL")
			return array(false,"Field 'Priority' must be numeric", "another try");

		$dp = new Dial_Plan;
		$dp->dial_plan_id = $this->dial_plan_id;
		$dp->priority = $this->priority;
		if(($msg = $dp->objectExists()))
			return array(false, (is_numeric($msg)) ? "This priority was already associated to another dial plan." : $msg, "another try");

		$res = array_merge(parent::setObj($params), array(2=>"another try"));
		return $res;
	}
}

?>