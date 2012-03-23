<?php
/**
 * settings.php
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

class Setting extends Model
{
	public static function variables()
	{
		return array(
					"setting_id" => new Variable("serial"),
					"param" => new Variable("text"),
					"value" => new Variable("text"),
					"description" => new Variable("text")
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	public static function defaultObject()
	{
		$params = array(
		    'vm'=>array('external/nodata/leavemaildb.php','Script used for leaving a voicemail message.'),
		    "version"=>'1', 
		    "annonymous_calls"=>array("no", "Allow calls from anomynous users if call is for one of the extensions. Use just 'yes' or 'no' as values."),
		    "international_calls"=>array("yes", "Allow calls to international/expensive destinations. This prefixes are set in Outbound>>International calls"), 
		    "international_calls_live"=>array("yes", "Allow calls to international/expensive destinations. This prefixes are set in Outbound>>International calls"),
		    "callerid"=>array("",""),
		    "callername"=>array("","")
		);
		$setting = new Setting;
		$nr_settings = $setting->fieldSelect("count(*)");
		if ($nr_settings>=count($params))
			return true;

		foreach($params as $key=>$value) {
			$description = NULL;
			if(is_array($value))
			{
				$description = $value[1];
				$value = $value[0];
			}
			$setting = new Setting;
			$setting->param = $key;
			$setting->select(array("param"=>$key));
			if($setting->setting_id) {
				/*if($setting->value != $value) {
					$setting->value = $value;
					$setting->description = $description;
					$setting->update();
				}*/
				continue;
			}else{
				$setting->description = $description;
				$setting->value = $value;
				$setting->insert();
			}
		}
		return true;
	}
}

?>