<?php
/**
 * short_names.php
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

class Short_Name extends Model
{
	public function variables()
	{ 
		return array(
				"short_name_id" => new Variable("serial", "!null"),
				"short_name" => new Variable("text", "!null"),
				"name" => new Variable("text"),
				"number" => new Variable("text", "!null"),
				"extension_id" => new Variable("serial", null, "extensions")
			);
	}

	function __construct()
	{
		parent::__construct();
	}	

	function setObj($params)
	{
		$this->short_name = strtolower(field_value("short_name",$params));

		$conditions = array("short_name"=>strtolower(field_value("short_name",$params)));
		if(field_value("extension_id", $params))
			$conditions["extension_id"] = strtolower(field_value("extension_id", $params));
		else
			$conditions["extension_id"] = "__empty";
		if($this->short_name_id)
			$conditions["short_name_id"] = "!=".$this->short_name_id;
		$nr = $this->fieldSelect("count(*)",$conditions);
		if($nr) 
			return array(false, "This 'Short name' is already defined.");
		$this->select();
		if($this->short_name_id && isset($params["extension_id"])) {
			if($this->extension_id != $params["extension_id"])
				return array(false, "You are not allowed to modify this short name.");
		}
		$this->setParams($params);
		$this->short_name = strtolower(field_value("short_name",$params));

		$match_number = self::getMatchingNumber($this->short_name);
		if(!$match_number) 
			return array (false,"You used invalid characters. You are only allowed to use the letters that you see on your phone's keypad.");

		$options = self::getPossibleOptions($match_number);
		$additional = ($this->extension_id) ? "extension_id='".$this->extension_id."' AND" : "extension_id IS NULL AND";
		$query = "SELECT count(*) FROM short_names WHERE $additional short_name IN ($options)";
		if($this->short_name_id)
			$query .= " AND short_name_id!=".$this->short_name_id;
		$res = Database::query($query);
		$res = query_to_array($res);

		if($res[0]["count"])
			return array(false, "This short name could be confused with another name. Please use another combination.");
		return parent::setObj($params);
	}

	public static function getMatchingNumber($name)
	{
		$alph = array(
					2 => array("a", "b", "c"),
					3 => array("d", "e", "f"),
					4 => array("g", "h", "i"),
					5 => array("j", "k", "l"),
					6 => array("m", "n", "o"),
					7 => array("p", "q", "r", "s"),
					8 => array("t", "u", "v"),
					9 => array("w", "x", "y", "z")
				);

		$number = '';
		for($l=0; $l<strlen($name); $l++)
		{
			$found = false;
			for($i=2; $i<10; $i++)
			{
				if(in_array($name[$l],$alph[$i])) {
					$found = true;
					break;
				}
			}
			if(!$found)
				return false;
			$number .= $i;
		}
		return $number;
	}

	public static function getPossibleOptions($number)
	{
		$posib = array();

		$alph = array(
					2 => array("a", "b", "c"),
					3 => array("d", "e", "f"),
					4 => array("g", "h", "i"),
					5 => array("j", "k", "l"),
					6 => array("m", "n", "o"),
					7 => array("p", "q", "r", "s"),
					8 => array("t", "u", "v"),
					9 => array("w", "x", "y", "z")
				);

		for($i=0; $i<strlen($number); $i++)
		{
			$digit = $number[$i];
			$letters = $alph[$digit];
			if(!count($posib)) {
				$posib = $letters;
				continue;
			}
			$s_posib = $posib;
			for($k=0; $k<count($letters); $k++)
			{
				if($k==0)
					for($j=0; $j<count($posib); $j++)
						$posib[$j] .= $letters[$k];
				else
					for($j=0; $j<count($s_posib); $j++)
						array_push($posib, $s_posib[$j].$letters[$k]);
			}
		}
		$options = implode("', '",$posib);
		return "'$options'";
	}
}
?>