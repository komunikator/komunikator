<?php
/**
 * gateways.php
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

class Gateway extends Model
{
	public static function variables()
	{
		return array(
					// compulsory fields
					"gateway_id" => new Variable("serial", "!null"),
					"gateway" => new Variable("text","!null"),
					"protocol" => new Variable("text", "!null"),
					"server" => new Variable("text"),

					"type" => new Variable("text","!null"), // reg, noreg

					// for gateways with registration
					"username" => new Variable("text"),
					"password" => new Variable("text"),
					"enabled" => new Variable("bool","t"),
					// various params that are not compulsory
					"description" => new Variable("text"),
					"interval" => new Variable("text"),
					"authname" => new Variable("text"),
					"domain" => new Variable("text"),
					"outbound" => new Variable("text"),
					"localaddress" => new Variable("text"),
					"formats" => new Variable("text"),

					// for sip gateways 
					"rtp_localip" => new Variable("text"),
					"ip_transport" => new Variable("text"),
					"oip_transport" => new Variable("text"),

					// for gateways without registrations
					//"ip" => new Variable("text"), -> was replaced with server
					"port" => new Variable("text"),
					"iaxuser" => new Variable("text"),
					"iaxcontext" => new Variable("text"),
					"formats" => new Variable("text"),

					"rtp_forward" => new Variable("bool"),
					"status" => new Variable("text"), // yate will set this field after trying to autenticate
					"modified" => new Variable("bool"),//field necesary for yate, autenticate again if modified is true
					"callerid" => new Variable("text"),
					"callername" => new Variable("text"),
					"send_extension" => new Variable("bool","f"), // if set call will keep extension in the caller parameter
					"trusted" => new Variable("bool","f"),	// allow calls from this gateway or it's incomming gateways to be routed to other gateways

					"sig_trunk_id" => new Variable("serial",null,"sig_trunks",true)
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	function getTableName()
	{
		return "gateways";
	}

	public function setObj($params)
	{
		$this->gateway = field_value("gateway", $params);
		if(($msg = $this->objectExists()))
			return array(false, (is_numeric($msg)) ? "This gateway already exists" : $msg, "another_try");
		$this->select();
		$this->setParams($params);

		if($this->type == "reg")
		{
			if($this->gateway_id)
				$compulsory = array("gateway", "username", "server");
			else
				$compulsory = array("gateway", "username", "password", "server");
			for($i=0; $i<count($compulsory); $i++)
				if(!$this->$compulsory[$i])
				return array(false, "Field '".$compulsory[$i]."' is required.", "another_try");
		}else{
			switch($this->protocol)
			{
				case "sip":
				case "h323":
					if(!$this->server)
						return array(false,"Field server is compulsory for the selected protocol.", "another_try");
					if(!$this->port)
						return array(false,"Field Port is compulsory for the selected protocol.", "another_try");
					if(Numerify($this->port) == "NULL")
						return array(false,"Field Port must be numeric.", "another_try");
					break;
			}
		}
		$res = parent::setObj($params);
		$this->modified = "t";
		$res = array_merge($res, array(2=>"another_try"));
		return $res;
	}
}

?>