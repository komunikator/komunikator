<?php
/**
 * dids.php
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

class DID extends Model
{
	public static function variables()
	{
		return array(
					"did_id" => new Variable("serial", "!null"),
					"did" => new Variable("text"),
					"number" => new Variable("text", "!null"),
					"destination" => new Variable("text", "!null"),
					"description" => new Variable("text"),
				//	"default_destination" => new Variable("text"),
					"extension_id" => new Variable("serial",NULL,"extensions",true),
					"group_id" => new Variable("serial",NULL,"groups",true)
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	function setObj($params)
	{
		$this->number = field_value("number",$params);
		if (Numerify($this->number) == "NULL")
			return array(false,"Field 'Number' must be numeric.");
		if (($msg = $this->objectExists()))
			return array(false, (is_numeric($msg)) ? "A DID for this number already exists." : $msg);

/*		$this->did = field_value("did",$params);
		$this->number = NULL;
		if (($msg = $this->objectExists()))
			return array(false,"A DID with this name already exists.");
*/
		if ($this->did_id)
			$this->select();
		$this->setParams($params);
		if (field_value("destination", $params) == "external/nodata/voicemaildb.php") {
			// if ddi is for voicemail default destination is not needed
			$this->extension_id = null;
			$this->group_id = null;
		}
		return parent::setObj($params);
	}
}
?>