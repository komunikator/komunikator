<?php
require_once("framework.php");

class Card_conf extends Model
{
	public static function variables()
	{
		return array(
					"param_name" => new Variable("text", "!null"),
					"param_value" => new Variable("text", "!null"),
					"section_name" => new Variable("text", "!null"),
					"module_name" => new Variable("text", "!null") // name of yate module that will handle this configuration
				);
	}
}
?>