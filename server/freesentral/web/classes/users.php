<?php
/**
 * users.php
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

class User extends Model
{
	public static function variables()
	{
		return array(
					"user_id" => new Variable("serial","!null"),
					"username" => new Variable("text","!null"),
					"password" => new Variable("text","!null"),
					"firstname" => new Variable("text"),
					"lastname" => new Variable("text"),
					"email" => new Variable("text"),
					"description" => new Variable("text"),
					"fax_number" => new Variable("text"),
					"ident" => new Variable("text"),
					"login_attempts" => new Variable("int2","0")
				);
	}

	function __construct()
	{
		parent::__construct();
	}

	public function login()
	{
		if (!$this->username || !$this->password)
			return NULL;
		$users = Model::selection("user", array("username"=>$this->username));
		if(count($users) == 1) 
		{
			if($users[0]->password == $this->password) {
				foreach($users[0] as $key=>$value)
				{
					$this->{$key} = $users[0]->{$key};
				}
				if($users[0]->login_attempts > 0) {
					$users[0]->login_attempts = 0;
					$users[0]->fieldUpdate(array("user_id"=>$users[0]->user_id), array("login_attempts"));
				}
				self::writeLog("admin ".$this->username. " logged in");
				return true;
			}else{
				$users[0]->login_attempts++;
				$users[0]->fieldUpdate(array("user_id"=>$users[0]->user_id), array("login_attempts"));
				self::writeLog("failled attempt to log in as admin: ".$this->username);
				return false;
			}
		}else{
			self::writeLog("failled attempt to log in as unknown admin: ".$this->username);
			return false;
		}
	}

	public static function defaultObject()
	{
		$username = 'admin';
		$password = 'admin';
		$user = new User;
		$nr_users = $user->fieldSelect("count(*)");
		if ($nr_users)
			return true;
		$user->username = $username;
		$user->password = $password;
		$res = $user->insert();
		return $res[0];
	}

	// show that this is the object that will be the peformer for logs
	public function isPerformer()
	{
		return array("performer_id"=>"user_id", "performer"=>"username", "real_performer_id"=>"");
	}

	public function setObj($params)
	{
		global $dont_change_admin_pass;

		if(isset($params["username"])) {
			$this->username = $params["username"];
			if(($msg = $this->objectExists()))
				return array(false, (is_numeric($msg)) ? 'Admin '.$this->username.' already exists.' : $msg);
		}
		if($this->user_id)
			$this->select();

		if(($this->username=="admin" || field_value("username",$params)=="admin") && $dont_change_admin_pass===true)
			unset($params["password"]);

		$this->setParams($params);
		if(strlen($this->password) < 5)
			return array(false, "Password  must be at least 5 digits long.");

		if(!check_valid_mail($this->email))
			return array(false, "Email address is not valid.");
		return parent::setObj($params);
	}
}

?>