<?php
/**
 * framework.php
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
/**
* the base classes for the database framework
*/

require_once("config.php");
require_once("debug.php");

// class for defining a variable that will be mapped to a column in a sql table
// name of variable must not be a numer or a numeric string
class Variable
{
	public $_type;
	public $_value;
	public $_key;
	public $_owner;
	public $_critical;
	public $_matchkey;
	public $_join_type;
	public $_required;
	/**
	 * Constructor for Variable class. Name of variable must not be a string
	 * @param $type Text representing the type of object: serial, text, int2, bool, interval etc
	 * @param $def_value Text or number representing the default value. Exception: if $def_value is set to !null then $required parameter is considered true. Exception was added so that we don't set a list false and null default params and to maintain compatibility
	 * @param $foreign_key Name of the table this column is a foreign key to. Unless $match_key is defined, this is a foreign
	 * key to a column with the same name in the $foreign_key table
	 * @param $critical Bool value. 
	 * true for ON DELETE CASCADE, if referenced row is deleted then this one will also be deleted
	 * false for ON DELETE SET NULL, if referenced is deleted then this column will be set to null
	 * @param $match_key Referenced variable name (Text representing the name of the column from the $foreign_key table to which this variable(column) will
	 * be a foreign key to).If not given the name is the same as this variable's
	 * @param $join_type Usually setted when extending objects. Possible values: LEFT, RIGHT, FULL, INNER. Default is LEFT is queries.
	 * @param $required Bool value specifing whether this field can't be null in the database. Default value is false
	 */
	function __construct($type, $def_value = NULL, $foreign_key = NULL, $critical = false, $match_key = NULL, $join_type = NULL, $required = false)
	{
		$this->_type = $type;
		$this->_value = (strtolower($def_value) != "!null") ? $def_value : NULL;
		$this->_key = $foreign_key;
		$this->_critical = $critical;
		$this->_owner = null;
		$this->_matchkey = $match_key;
		$this->_join_type = $join_type;
		$this->_required = ($required === true || strtolower($def_value) == "!null") ? true : false;
	}

	/**
	 * Returns the correctly formated value of a Variable object. Value will be used inside a query. Formating is done 
	 * according to the type of the object
	 * @param $value Instance of Variable class
	 * @return Text or number to be used inside a query
	 */
	public function escape($value)
	{
		if (!strlen($value) && $this->_type != "bool")
			return 'NULL';

		$value = trim($value);
		switch ($this->_type)
		{
			case "bool":
				if($value === true || $value == 't')
					return "'t'";
				else
					return "'f'";
				break;
			case "int":
			case "int2":
			case "int4":
			case "float4":
			case "float8":
			case "serial":
			case "bigserial":
				$value = str_replace(',','',$value);
				return 1*$value;
				break;
			case "interval":
				if($value) {
					return "interval '".$value." s'";
					break;
				}
			default:
				return "'" . Database::escape($value) . "'";
		}
	}

	public function isRequired()
	{
		return $this->_required;
	}
}

// class that does the operations with the database 
class Database
{
	protected static $_connection = true;

	/**
	 * Make database connection
	 * @return The connection to the database. If the connection is not possible, page dies
	 */
	public static function connect()
	{
		global $db_host,$db_user,$db_database,$db_passwd;

		if(!function_exists("pg_connect"))
			die("You don't have php-pgsql package installed.");

		if (self::$_connection === true)
			self::$_connection = pg_connect("host='$db_host' dbname='$db_database' user='$db_user' password='$db_passwd'") or die("Could not connect to the database");
		return self::$_connection;
	}

	/**
	 * Start transaction
	 */
	public static function transaction()
	{
		return Database::query("BEGIN WORK");
	}

	/**
	 * Perform roolback on the current transaction
	 */
	public static function rollback()
	{
		return Database::query("ROLLBACK");
	}

	/**
	 * Commit current tranction
	 */
	public static function commit()
	{
		return Database::query("COMMIT");
	}

	/**
	 * Perform query.If query fails, unless query is a ROLLBACK, it is supposed that the database structure changed. Try to 
	 * modify database structure, then perform query again using the  queryRaw() method
	 * @param $query Text representing the query to perform
	 * @return Result received after the performing of the query 
	 */
	public static function query($query)
	{
		if (!self::connect())
			return false;

		if(isset($_SESSION["debug_all"]))
//			print "<br/>\n<br/>\nquery :'.$query.'<br/>\n<br/>\n";
			Debug::output("query: $query");

		if (!self::is_single_query($query))
			return false;

		if (function_exists("pg_result_error_field"))
		{
			// happy, happy, joy, joy!
			/*if (!pg_connection_busy(self::$_connection)) 
			{
				$ok = pg_send_query(self::$_connection,$query);
			}
			$res = pg_get_result(self::$_connection);
			
			if ($ok)*/
			$res = pg_query(self::$_connection,$query);
			if($res && $query != "ROLLBACK") //if query is a ROLLBACK then we got an error somewhere
				return $res;
			else
			{
				if(!Model::modified())
				{
					Model::updateAll();
					return self::queryRaw($query);
				}
				else
					return $res;
			}
		}else{
			// we'll do our best which is not much...
			$res = pg_query(self::$_connection,$query);
			if ($res  && $query != "ROLLBACK")
				return $res;
			else
			{
				if(!Model::modified())
				{
					Model::updateAll();
					return self::queryRaw($query);
				}
				else
					return $res;
			}
		}
	}

	/**
	 * Make sure queries separated by ; won't we run
	 * @param $query String that will be verified
	 * @return Bool - true if single query, false otherwise
	 */
	public static function is_single_query($query)
	{
	 	$pattern = "/'[^']*'/";
		$replacement = "";
		// all that is in between '' is ok
		$mod_query = preg_replace($pattern,$replacement,$query);
		// after striping all '..' if we still have ; then query is composed of multiple queries
		if (strpos($mod_query,";"))
			return false;   
		return true;
	}

	/**
	 * Perform query without verifying if it failed or not
	 * @param $query Text representing the query to perform
	 * @return Result received after the performinng of the query 
	 */
	public static function queryRaw($query)
	{
		if (!self::connect())
			return false;
		if(isset($_SESSION["debug_all"]))
//			print "queryRaw: $query\n<br/>\n<br/>\n";
			Debug::output("queryRaw: $query");
		if (!self::is_single_query($query))
			return false;
		return pg_query(self::$_connection,$query);
	}

	/**
	 * Create corresponding sql table for this object
	 * @param $table Name of the table
	 * @param $vars Array of variables for this object
	 * @return Result returned by performing the query to create this $table  
	 */
	public static function createTable($table,$vars)
	{
		if (!self::connect())
			return false;
		$query = "";

		$nr_serial_fields = 0;

		foreach ($vars as $name => $var)
		{
			if(is_numeric($name))
			{
				//  do not allow numeric names of columns
				exit("You are trying to add a column named $name, numeric names of columns are not allowed.");
			}
			$type = $var->_type;
			switch ($type)
			{
				case "serial":
					if ($var->_key != "")
						$type = "int4";
					break;
				case "bigserial":
					if ($var->_key != "")
						$type = "int8";
					break;
			}
			if ($query != "")
				$query .= ",";

			if($type == "serial" || $type == "bigserial")
				$nr_serial_fields++;

			$query .= "\"$name\" $type";
		}

		// do not allow creation of tables with more than one serial field
		// protection is inforced here because i rely on the fact that there is just one numeric id or none
		if($nr_serial_fields > 1)
			exit("Error: Table $table has $nr_serial_fields serial or bigserial fields. You can use 1 serial or bigserial field per table or none.");

		$query = "CREATE TABLE $table ($query) WITH OIDS";
		return self::queryRaw($query) !== false;
	}

	/**
	 * Update the structure of the table
	 * @param $table Name of the table
	 * @param $vars Array of variables for this object
	 * @return Bool value showing if the update succeeded or not
	 */
	public static function updateTable($table,$vars)
	{
		if (!self::connect())
			return false;
		$query = "SELECT * FROM $table WHERE false";
		$res = self::queryRaw($query);
		if (!$res)
		{
		//	print "Table '$table' does not exist so we'll create it\n";
			return self::createTable($table,$vars);
		}
		foreach ($vars as $name => $var)
		{
			$type = $var->_type;
			$field = pg_field_num($res,$name);
			if ($field < 0) 
				$field = pg_field_num($res,"\"$name\"");
			if ($field < 0)
			{
				if($type == "serial")
					$type = "int4";
				if($type == "bigserial")
					$type = "int8";
			//	print "No field '$name' in table '$table', we'll create it\n";
				$query = "ALTER TABLE $table ADD COLUMN \"$name\" $type";
				if (!self::queryRaw($query))
					return false;
				if ($var->_value !== null)
				{
					$val = $var->escape($var->_value);
					$query = "UPDATE $table SET \"$name\"=$val";
					if (!self::queryRaw($query))
						return false;
				}
			}
			else
			{
				// we need to adjust what we expect for some types
				switch ($type)
				{
					case "serial":
						$type = "int4";
						break;
					case "bigserial":
						$type = "int8";
						break;
				}
				$dbtype = pg_field_type($res,$field);
				if ($dbtype == $type)
					continue;
				Model::warning("Field '$name' in table '$table' is of type '$dbtype' but should be '$type'\n");
				return false;
			}
		}
		return true;
	}

	/**
	 * Creates one or more btree indexs on the specified table
	 * @param $table Name of the table
	 * @param $columns Array with the columns for defining each index
	 * Ex: array("time") will create an index with the name "$table-time" on column "time"
	 * array("index-time"=>"time", "comb-time-user_id"=>"time, user_id") creates an index called "index-time" on column "time"
	 * and an index called "comb-time-user_id" using both "time" and "user_id" columns
	 * @return Bool value showing if the creating succeeded or not 
	 */
	public static function createIndex($table, $columns)
	{
		$no_error = true;
		$make_vacuum = false;
		foreach($columns as $index_name=>$index_columns)
		{
			if ($index_columns == '' || !$index_columns)
				continue;
			if(is_numeric($index_name))
				$index_name = "$table-index";
			$query = "CREATE INDEX \"$index_name\" ON $table USING btree ($index_columns)";
			$res = self::queryRaw($query);
			if (!$res)
			{
				$no_error = false;
				continue;
			}
			$make_vacuum = true;
		//	$res = self::queryRaw($query);
		}
		if($make_vacuum)
		{
			$query = "VACUUM ANALYZE $table";
			$res = self::queryRaw($query);
		}
		return $no_error;
	}

	public static function escape($value)
	{
		return pg_escape_string(self::connect(), $value);
	}

	public static function unescape($value)
	{
		return $value;
	}
}

// general model for defining an object that will be mapped to an sql table 
class Model
{
	protected $_model;
	//if $_invalid is setted to true, this object can't be setted as a key for update or delete clauses 
	protected $_invalid;
	//whether the params for a certain object were set or not. It is setted to true in setParams dar is called from setObj
	protected $_setted;
	//whether a select or extendedSelect was performed on the object 
	protected $_retrieved;

	protected static $_models = false;
	protected static $_modified = false;
	// array with name of objects that are performers when using the ActionLog class
	protected static $_performers = array();

	/**
	 * Base class constructor, populates the list of variables of this object
	 */
	function __construct()
	{
		$this->_invalid = false;
		$this->_model = self::getVariables(get_class($this));
		$this->_setted = false;
		$this->_retrieved = false;
		foreach ($this->_model as $name => $var) {
			if($name == "__sql_relation")
				exit("Invalid column name: __sql_relation.");
			$this->$name = $var->_value;
		}
	}

	/**
	 * Creates an array of objects by selecting rows that match the conditions.
	 * @param $class Name of the class of the returned object
	 * @param $conditions Array of conditions of type $key=>$value
	 * @param $order Array used for specifing order options OR just Text with options
	 * @param $limit Number used for setting the LIMIT clause of the query
	 * @param $offset Number used for seeting the OFFSET clause of the query
	 * @param $given_where Text with the conditions correctly formatted
	 * @param $inner_query Array used for defining an inner query inside the current query.
	 * See method @ref makeInnerQuery() for more detailed explanation
	 * @return array of objects of type $class.
	 */
	public static function selection($class, $conditions=array(), $order= NULL, $limit=NULL, $offset=0, $given_where = NULL, $inner_query=NULL)
	{
		$vars = self::getVariables($class);
		if (!$vars) 
		{
//			print '<font style="weight:bold;">You haven\'t included file for class '.$class.' try looking in ' . $class . 's.php</font>';
			Debug::output('<font style="weight:bold;">You haven\'t included file for class '.$class.' try looking in ' . $class . 's.php</font>');
			return null;
		}
		$table = self::getClassTableName($class);
		$obj = new $class;
		$where = ($given_where) ? $given_where : $obj->makeWhereClause($conditions,true);
		if ($inner_query)
			$where = $obj->makeInnerQuery($inner_query, $table, $where);
		$query = self::buildSelect("*", $table, $where, $order, $limit, $offset);
		$res = Database::query($query);
		if(!$res)
		{ 
			self::warning("Could not select $class from database in selection.");
			return null;
		}
	//	$object = new $class;
		return $obj->buildArrayOfObjects($res);
	}

	/**
	 * Perform query with fields given as a string.
	 * Allows using sql functions on the results being returned
	 * @param $fields String containing the items to select. 
	 * Example: count(user_id), max(price), (case when $condition then value1 else value2 end) as field_name
	 * @param $conditions Array of conditions
	 * @param $group_by String for creating GROUP BY clause
	 * @param $order Array or String for creating ORDER clause
	 * @param $inner_query Array for using an inner query inside the WHERE clause
	 * @param $extend_with Array of $key=>$value pair, $key is the name of a column, $value is the name of the table
	 * Example: class of $this is Post and $extended=array("category_id"=>"categories") 
	 * becomes AND post.category_id=categories.category_id 
	 * @param $given_where String representing the WHERE clause
	 * @param $given_from String representing the FROM clause
	 * @return Value for single row, sigle column in sql result/ Array of results : $result[row][field_name] 
	 * if more rows or columns where returned 
	 */
	public function fieldSelect($fields, $conditions=array(), $group_by=NULL, $order=NULL, $inner_query=NULL, $extend_with=NULL, $given_where=NULL, $given_from=NULL)
	{
		$table = $this->getTableName();
		$class = get_class($this);

		$where = ($given_where) ? $given_where : $this->makeWhereClause($conditions, true);
		$where = $this->makeInnerQuery($inner_query, $table, $where);

		$from_clause = ($given_from) ? $given_from : $table;
		$clause = '';
		$tables = str_replace(' ', '',$from_clause);
		$tables = explode(',',$tables);
		if(count($extend_with))
		{
			foreach($extend_with as $column_name=>$table_name)
			{
				if(!in_array($table_name, $tables))
				{
					$from_clause .= ', '.$table_name;
					array_push($tables, $table_name);
				}
				if ($clause != '')
					$clause .= ' AND ';
				$clause .= "\"$table\".\"$column_name\"=\"$table_name\".\"$column_name\"";
			}
		}
		if ($where != '' && $clause != '')
			$where .= ' AND '.$clause;
		elseif($clause != '' && $where == '')
			$where = "WHERE $clause";

		$query = self::buildSelect($fields, $from_clause, $where, $order, NULL, NULL, $group_by);
		$res = Database::query($query);
		if(!$res)
		{ 
			self::warning("Could not select $class from database");
			return null;
		}elseif(!pg_num_rows($res))
			return null;

		if (pg_num_rows($res) == 1 && pg_num_fields($res) == 1)
			return pg_fetch_result($res,0,0);
		for($i=0; $i<pg_num_rows($res); $i++)
		{
			$array[$i] = array();
			for($j=0; $j<pg_num_fields($res); $j++)
				$array[$i][pg_field_name($res,$j)] = htmlentities(Database::unescape(pg_fetch_result($res,$i,$j)));
		}
		return $array; 
	}

	/**
	 * Fill $this object from it's corresponsing row in the database
	 * @param $condition 
	 * $condition should be NULL if we wish to use the value of the numeric id for the corresponding table
	 * $condition should be a STRING representing the name of a column that could act as primary key
	 * In both the above situations $this->{$condition} must be setted before calling this method
	 * $condition should be an ARRAY formed by pairs of $key=>$value for more complex conditions 
	 * This is the $conditions array that will be passed to the method @ref makeWhereClause()
	 */
	public function select($condition = NULL)
	{
		$vars = self::getVariables(get_class($this));
		$table = $this->getTableName();
		$class = get_class($this);
		if (!is_array($condition))
		{
			if(!$condition)
			{
				if(!($id = $this->getIdName()))
				{
					$this->invalidate();
					return;
				}
				$condition = $id;
			}
			$var = $this->variable($condition);
			$value_condition = ($this->{$condition}) ? $var->escape($this->{$condition}) : NULL;
			if (!isset($this->{$condition}) || !$value_condition)
			{
				$this->invalidate();
				return;
			}
			$where_clause = " WHERE \"$condition\"=".$value_condition;
		} else
			$where_clause = $this->makeWhereClause($condition, true);

		$query = self::buildSelect("*", $table, $where_clause);
		$res = Database::query($query);
		if(!$res)
		{ 
			self::warning("Could not select $class from database. Query: $query");
			$this->invalidate();
			return ;
		} elseif(!pg_num_rows($res)) {
			$this->invalidate();
			return;
		} elseif(pg_num_rows($res)>1) {
			$this->invalidate();
			self::warning('More results for a single id.');
			return;
		} else
			$this->populateObject($res);
	}

	/**
	 * Populates $this object, that could have been previously extended, with the fields returned by the generated query or Returns an array of objects 
	 * @param $conditions Array of conditions or pairs field_name=>value
	 * @param $order String representing the order or Array of pairs field_name=>"ASC"/"DESC"
	 * @param $limit Integer representing the maximum number or objects to be returned
	 * @param $offset Integer representing the offset
	 * @param $inner_query Array used for defining an inner query
	 * @param $given_where	WHERE clause already formated
	 * @return NULL if a single row was returned and method was called without any of the above params, $this object is populated with the results of the query / Array of objects having the same variables as $this object
	 */
	public function extendedSelect($conditions = array(), $order = NULL, $limit = NULL, $offset = NULL, $inner_query = array(), $given_where = NULL)
	{
		// the array of variables from the object
		$vars = $this->_model;
		$table = $this->getTableName();
		$id = $this->getIdName();
		// array holding the columns that were added to the query
		$added_fields = array();
		// array of the tables that will appear in the FROM clause 
		$from_tables = array();
		// 
		$from_tables[$table] = true;

		$from_clause = '';
		$columns = '';
		foreach($vars as $var_name => $var)
		{
			// name of table $var_name is a foreign key to
			$foreign_key_to = $var->_key;
			// name of variable in table $foreign_key_to $var_name references 
			$references_var = $var->_matchkey;
			$join_type = $var->_join_type;
			if(!$join_type)
				$join_type = "LEFT";
			if ($columns != '')
				$columns .= ', ';
			$added_fields[$var_name] = true;

			if($foreign_key_to)
			{
				if($from_clause == '')
					$from_clause = " \"$table\" ";
				else
					$from_clause = " ($from_clause) ";
			}

			// if this is not a foreign key to another table and is a valid variable inside the corresponding object
			if(!$foreign_key_to && self::inTable($var_name,$table))
				$columns .= " \"$table\".\"$var_name\"";
			// if this is a foreign key to another table, but does not define a recursive relation inside the $table
			elseif($foreign_key_to && $foreign_key_to != $table) {
				if($references_var)
					// when extending one might need to use another name than the one used in the actual table
					// prime reason: common field names as status or date are found in both tables
					$columns .= " \"$foreign_key_to\".\"$references_var\" as \"$var_name\" ";
				else
					$columns .= " \"$foreign_key_to\".\"$var_name\"";
				// this table was already added in the FROM clause
				if(isset($from_tables[$foreign_key_to])) {
					if($join_type != "LEFT") {
						// must overrite old join type with new join type
						$from_clause = str_replace("LEFT OUTER JOIN \"$foreign_key_to\"", "$join_type OUTER JOIN \"$foreign_key_to\"", $from_clause);
					}
					continue;
				}
				// if $var_name was not added by extending the object, but it's inside the $class of the object 

				$from_tables[$foreign_key_to] = true;
				if(self::inTable($var_name,$table))
				{
					// must add table inside the FROM clause and build the join
					$key = ($references_var) ? $references_var : $var_name;
					$from_clause .= "$join_type OUTER JOIN \"$foreign_key_to\" ON \"$table\".\"$var_name\"=\"$foreign_key_to\".\"$key\"";
					continue;
				}
				// keeping in mind that the $var_name fields that were added using the extend method are always at the end 
				// in the $vars array: 
				// if we reach here then $var_name is a field in the table represented by foreign_key_to and that table has a foreign key to the table corresponding to this object, and not the other way around
				// Example: i have an instance of the group class, i extend that object sending 
				// array("user"=>"users") as param to extend() method, but when query is made i need to look in the
				// user object to find the key that links the two tables 

				$obj = self::getObject($foreign_key_to);
				$obj_vars = $obj->_model;

				$continue = false;
				foreach($obj_vars as $varname => $obj_var)
				{
					if($obj_var->_key != $table)
						continue;
					$referenced = ($obj_var->_matchkey) ? $obj_var->_matchkey : $varname;
					$from_clause .= "$join_type OUTER JOIN \"$foreign_key_to\" ON \"$table\".\"$referenced\"=\"$foreign_key_to\".\"$varname\"";
					//found the condition for join=> break from this for and the continue in the loop to pass to the next var 
					$continue = true;
					break;
				}
				if($continue)
					continue;
				// if i get here then the object was wrongly extended OR user wanted to get a cartesion product of the two tables: 
				// $table does not have a foreign key to $foreign_key_to table
				// $foreign_key_to table oes not have a foreign key to $table
				self::warning("No rule for extending table '$table' with field '$var_name' from table '$foreign_key_to'. Generating cartesian product.");
				$from_clause .= ", $foreign_key_to ";
			}elseif($foreign_key_to && $foreign_key_to == $table) {
				// this defines a recursive relation inside the same table, just 1 level
				if(self::inTable($var_name,$table)) {
					$columns .= " $table".'1'.".\"$references_var\" as \"$var_name\"";
					$from_clause .= "$join_type OUTER JOIN \"$foreign_key_to\" as $foreign_key_to" . "1" . " ON \"$table\".\"$var_name\"=$foreign_key_to"."1".".\"$references_var\"";
				} else {
					// 1 level recursive relation
					$columns .= " $table".'1'.".\"$references_var\" as \"$var_name\"";
				}
			}
		}
		if ($from_clause == '')
			$from_clause = $table;

		if(!$given_where) 
		{
			$where = $this->makeWhereClause($conditions);
			$where = $this->makeInnerQuery($inner_query, $table, $where);
		}else
			$where = $given_where;

		if(!is_array($order))
			if(substr($order,0,3) == 'oid')
				// if we need to order by the oid then add oid to the columns
				$columns = 'distinct '.$my_table.'.oid,'.$columns;

		// we asume that this query will return more than one row
		$single_object = false;
		if($id)
		{
			$var = $this->variable($id);
			$value_id = $this->{$id};
			//if this object has a numeric id defined, no conditions were given then i want that object to be returned
			if (!count($conditions) && !$given_where && !$order && !$limit && !$offset && $value_id)
			{
				$value_id = $var->escape($value_id);
				// one expectes a single row to be returned from the resulted query
				$where = "WHERE \"$table\".\"$id\"=".$value_id;
				$single_object =true;
			}
		}

		$query = self::buildSelect($columns, $from_clause,$where,$order,$limit,$offset); 
		$res = Database::query($query);
		if(!$res)
		{
			$this->invalidate(); 
			self::warning("Could not select ".get_class($this)." from database. Query: $query");
			return ;
		}

		if (pg_num_rows($res) == 1 && $single_object)
			$this->populateObject($res);
		else
			return $this->buildArrayOfObjects($res);
	}

	/**
	 * Set the params for this object in the database.
	 * @param $params Array of type $field_name => $field_value
	 * @return Array where [0] is bool value showing whether query succesed , [1] is the default message
	 */
	public function setObj($params)
	{
		if(!$this->_setted)
			$this->setParams($params);
		return array(true, '', array());
	}

	/**
	 * Set the variables of object and insert it in the database
	 * @param $params Array of param_name=>param_value used for setting the variables of this object
	 * @param $retrieve_id Bool value, true if you want the id of the inserted object to be retrived or not
	 * @param $keep_log Bool value, true when you wish to insert a log entry for that action
	 * @return Array, array[0] is true/false, true when inserting was succesfull, array[1] default message to could be printed to the user, array[2] is array with fields there was an error with
	 */
	public function add($params=NULL, $retrieve_id=true, $keep_log=true)
	{
		if($params) {
			$res = $this->verifyRequiredFields($params);
			if(!$res[0])
				return $res;
			$res = $this->setObj($params);
			if(!$res[0])
				return $res;
		}
		return $this->insert($retrieve_id, $keep_log);
	}

	/**
	 * Set the variables of object and insert it in the database
	 * @param $params Array of param_name=>param_value used for setting the variables of this object
	 * @param $conditons Array of conditions after which to do the update. Default is NULL(update after object id)
	 * @param $verifications Array with conditions trying to specify if this object can be modified or not
	 * @return Array, array[0] is true/false, true when inserting was succesfull, array[1] default message to could be printed to the user, array[2] is array with fields there was an error with
	 */
	public function edit($params, $conditions = NULL, $verifications = array())
	{
		if($params) {
			$res = $this->verifyRequiredFields($params);
			if(!$res[0])
				return $res;
			$res = $this->setObj($params);
			if(!$res[0])
				return $res;
		}
		return $this->update($conditions, $verifications);
	}

	/**
	 * Set the params for $this object
	 */
	public function setParams($params)
	{
		$this->_setted = true;

		foreach($params as $param_name=>$param_value)
			if($this->variable($param_name))
				$this->{$param_name} = $param_value;
	}

	/**
	 * Insert this object in the database
	 * @param $retrieve_id BOOL value marking if we don't wish the numeric id of the inserted object to be returned
	 * By default the numeric id of the object is retrieved
	 * @param $keep_log BOOL value marking whether to log this operation or not
	 * @return Array(Bool value,String), bool shows whether the inserting succeded or not, String is a default message that could be printed 
	 */
	public function insert($retrieve_id = true, $keep_log = true)
	{
		$columns = "";
		$values = "";
		$serials = array();
		$insert_log = "";
		$error = "";
		$error_fields = array();
		foreach ($this->_model as $var_name => $var)
		{
			$value = $this->$var_name;
			if (!strlen($value))
			{
				// some types have defaults assigned by DB server so we don't set them
				switch ($var->_type)
				{
					case "serial":
						if ($var->_key == "") {
							$serials[$var_name] = true;
							$var = null;
						}
						break;
					case "bigserial":
						if ($var->_key == "") {
							$serials[$var_name] = true;
							$var = null;
						}
						break;
				}
			}
			if (!$var)
				continue;
			if (!strlen($value) && $var->isRequired()) {
				$error .= " Required field '".$var_name."' not set.";
				$error_fields[] = $var_name;
				// gather other errors as well
				continue;
			}
			if ($columns != "")
			{
				$columns .= ",";
				$values .= ",";
				$insert_log .= ", ";
			}
			$columns .= "\"$var_name\"";
			$values .= $var->escape($value);
			$insert_log .= "$var_name=".$value;
		}
		if ($columns == "")
			return;
		$table = $this->getTableName();

		if($error != "")
			return array(false, "Failed to insert into $table.".$error, $error_fields);

		$query = "INSERT INTO \"$table\"($columns) VALUES($values)";
		$res = Database::query($query);
		if (!$res)
			return array(false,"Failed to insert into $table.",$error_fields);
		if($retrieve_id)
			if (count($serials))
			{
				$oid = pg_last_oid($res);
				if ($oid === false)
					return array(false,"There are no OIDs on table $table",array());
				$columns = implode(",",array_keys($serials));
				$query_oid = "SELECT $columns FROM $table WHERE oid=$oid";
				$res = Database::query($query_oid);
				if (!$res)
					return array(false,"Failed to select serials",array());
				foreach (array_keys($serials) as $var_name)
					$this->$var_name = pg_fetch_result($res,0,$var_name);
			}
		$log = "inserted ".$this->getNameInLogs().": $insert_log";
		if($keep_log === true)
			self::writeLog($log,$query);
		return array(true,"Succesfully inserted into ".ucwords(str_replace("_"," ",$table)),array());
	}

	/**
	 * Build insert query for this $object
	 * @return Text representing the query
	 */
	public function buildInsertQuery()
	{
		$columns = "";
		$values = "";
		foreach ($this->_model as $var_name => $var)
		{
			$value = $this->$var_name;
			if (!$value)
				continue;
			if ($columns != "")
			{
				$columns .= ",";
				$values .= ",";
			}
			$columns .= "\"$var_name\"";
			$values .= $var->escape($value);
		}
		if ($columns == "")
			return;
		$table = $this->getTableName();

		$query = "INSERT INTO \"$table\"($columns) VALUES($values)";
		return $query;
	}
	
	/**
	 * Update object (!! Use this after you selected the object or else the majority of the fields will be set to null)
	 * @param $conditions Array of conditions for making an update
	 * if no parameter is sent when method is called it will try to update based on the numeric id od the object, unless is was invalidated
	 * @param $verifications Array with conditions trying to specify if this object can be modified or not
	 * @return Array(BOOL value, String, Array, Int), boolean markes whether the update succeeded or not, String is a default message that might be printed, Array with name of fields there was a problem with, Int shows the number of affected rows
	 */
	public function update($conditions = array(), $verifications = array())
	{
		$where = "";
		$variables = "";
		$update_log = "";
		$error = "";
		$error_fields = array();
		if (!count($conditions))  {
			if($this->isInvalid())
				return array(false, "Update was not made. Object was invalidated previously.",$error_fields, 0);
			$id = $this->getIdName();
			if(!$id || !$this->{$id})
				return array(false, "Don't have conditions to perform update.",$error_fields,0);
			$conditions = array($id=>$this->{$id});
		}
		// add the received verifications to the conditions
		if($verifications)
			$conditions = array_merge($conditions, $verifications);
		$where = $this->makeWhereClause($conditions, true);
		$vars = self::getVariables(get_class($this));
		if (!$vars)
			return null;
		foreach($vars as $var_name=>$var) 
		{
			if ($variables != '')
			{
				$variables .= ", ";
				$update_log .= ", ";
			}

			if(!strlen($this->{$var_name}) && $var->isRequired()) {
				$error .= " Required field '".$var_name."' not set.";
				$error_fields[] = $var_name;
				continue;
			}

			$value = $var->escape($this->{$var_name});
			$variables .= "\"$var_name\""."=".$value."";
			$update_log .= "$var_name=".$this->{$var_name}.""; 
		}
		$obj_name = $this->getObjectName();
		if($error != "")
			return array(false,'Failed to update '.$obj_name.".".$error, $error_fields,0);
		$table = $this->getTableName();
		$query = "UPDATE \"$table\" SET $variables $where";
		//print "query-update:$query";
		$res = Database::query($query);
		if(!$res) 
			return array(false,'Failed to update '.$obj_name.".",array(),0);
		else{
			$message = 'Succesfully updated '.pg_affected_rows($res).' ' .$obj_name;
			if (pg_affected_rows($res) != 1)
				$message .= 's';
			$update_log = "updated ".$this->getNameInLogs().": $update_log $where";
			self::writeLog($update_log,$query);
			return array(true,$message,array(),pg_affected_rows($res));
		}
	}

	/**
	 * Update only the specified fields for this object
	 * @param $conditions NULL for using the id / Array on pairs $key=>$value
	 * @param $fields Array($field1, $field2 ..)
	 * @param $verifications Array with conditions trying to specify if this object can be modified or not
	 * @return Array(Bool,String,Array,Int) Bool whether the update succeeded or not, String is a default message to print, 
	 * Array with name of fields there was a problem with -always empty for this method, Int is the number of affected rows 
	 */
	public function fieldUpdate($conditions = array(), $fields = array(), $verifications = array())
	{
		$where = "";
		$variables = "";
		$update_log = "";

		if(!count($conditions)) {
			if($this->isInvalid())
				return array(false, "Update was not made. Object was invalidated previously.",array(),0);
			$id = $this->getIdName();
			if(!$id || !$this->{$id})
				return array(false, "Don't have conditions to perform update.", array(),0);
			$conditions = array($id=>$this->{$id});
		}
		if($verifications)
			$conditions = array_merge($conditions, $verifications);

		$where = $this->makeWhereClause($conditions, true);
		$vars = self::getVariables(get_class($this));
		if (!count($fields))
			return array(false,"Update was not made. No fields were specified.",array(),0);
		foreach($vars as $var_name=>$var) 
		{
			if(!in_array($var_name,$fields))
				continue;
			if(!isset($vars[$var_name]))
				continue;

			if($variables != '')
			{
				$variables .= ", ";
				$update_log .= ", ";
			}

			$value = $this->{$var_name};
			if(substr($value,0,6) == "__sql_")
			{
				//Value is an sql function or other column from the same table
				//When using this and referring to a column named the same as a reserved word 
				//in PostgreSQL "" must be used inside the $value field
				$value = substr($value,6,strlen($value));
				$variables .= "\"$var_name\""."="."$value";
			}else{
				$variables .= "\"$var_name\""."=".$var->escape($value)."";
			}
			$update_log .= "$var_name=$value";
		}

		$obj_name = $this->getObjectName();
		$query = "UPDATE ".$this->getTableName()." SET $variables $where";
		$res = Database::query($query);
		if(!$res) 
			return array(false,'Failed to update '.$obj_name,array(),0);
		else
		{
			$mess = 'Succesfully updated '.pg_affected_rows($res).' ' .$obj_name;
			if (pg_affected_rows($res) != 1)
				$mess .= 's';
			$update_log = "update ".$this->getNameInLogs().": $update_log $where";
			self::writeLog($update_log,$query);
			return array(true,$mess,array(),pg_affected_rows($res));
		}
	}

	/**
	 *	Verify if the required fields for this object will be set.
	 * @param $params Array of type $param=>$value. Only the required fields appearing in this array will be verified
	 * @return Array(Bool, Text, Array) Bool true if all required fields in $params are set, Text with the error message, Array with the name of the fields that were not set
	 */
	public function verifyRequiredFields($params)
	{
		$error_fields = array();
		$error = "";
		$class = get_class($this);
		foreach ($params as $param_name => $param_value) {
			$var = Model::getVariable($class, $param_name);
			if (!$var)
				continue;
			if ($var->_required === true && (!strlen($param_value) || $param_value =='')) {
				if ($error != "")
					$error .= ", ";
				$error .= "Field '".str_replace("_"," ",$param_name)."' is required";
				$error_fields[] = $param_name;
			}
		}
		if ($error != "") {
			$error .= ".";
			return array(false, $error, $error_fields);
		} else
			return array(true, "", array());
	}
	
	 /**
	  * Verify if there is an entry in the database for the object of the given class
	  * @param $param Name of the field that we don't want to have duplicates
	  * @param $value Value of the field @ref $param
	  * @param $class Object class that we want to check for
	  * @param $id Name of the id field for the type of @ref $class object
	  * @param $value_id Value of the id
	  * @param $additional Other conditions that will be writen directly in the query
	  * @return true if the object exits, false if not
	  */
	public static function rowExists($param, $value, $class, $id, $value_id = NULL, $additional = NULL)
	{
		$table = self::getClassTableName($class);

		$value_id = pg_escape_string($value_id);
		if ($value_id)
			$query = "SELECT $id FROM $table WHERE \"$param\"='".Database::escape($value)."' AND $id!='$value_id' $additional";
		else
			$query = "SELECT $id FROM $table WHERE \"$param\"='".Database::escape($value)."' $additional";
		$res = Database::query($query);
		if(!$res)
			exit("Could not do: $query");
		if(pg_num_rows($res))
			return true;
		return false;
	}

	 /**
	  * Verify if an object has an entry in the database. Before this method is called one must set the values of the fields that will build the WHERE clause. Variables that have a default value will be ignored
	  * @param $id_name Name of the id of this object
	  * @return id or the object that matches the conditions, false otherwise
	  */
	public function objectExists($id_name = NULL)
	{
		$vars = self::getVariables(get_class($this));
		if (!$vars)
			return null;

		if(!$id_name) 
			$id_name = $this->getIdName();

		$class = get_class($this);
		if(!$id_name || !$this->variable($id_name))
		{
			self::warning("$id_name is not a defined variable inside the $class object.");
			exit();
		}

		$fields = '';
		$table = $this->getTableName();

		//get an object of the same class as $this
		$clone = new $class;

		$conditions = array();
		foreach($vars as $var_name=>$var) 
		{
			// ignoring fields that have a default value and the numeric id of the object
			if ($this->{$var_name} != '' && $var_name != $id_name) 
			{
				if($clone->{$var_name} != '')
					continue;
				$conditions[$var_name] = $this->{$var_name};
			}
		}

		$var = $this->variable($id_name);
		$value_id = $var->escape($this->{$id_name});
		$where = ($value_id && $value_id != "NULL") ? "WHERE $fields AND \"$id_name\"!='$value_id'" : "WHERE $fields";

		if($value_id && $value_id != "NULL")
			$conditions[$id_name] = "!=".$value_id;
		$where = $this->makeWhereClause($conditions,true);
		$query = self::buildSelect($id_name,$table,$where);
		$res = Database::query($query);

		if(!$res) {
//			print ("Operation was blocked because query failed: '$query'.");
			Debug::output("Operation was blocked because query failed: '$query'.");
			return true;
		}

		if(pg_num_rows($res)) {
			return pg_fetch_result($res,0,0);
		}
		return false;
	}

	/**
	 * Recursive function that deletes the object(s) matching the condition and all the objects having foreign keys to
	 * this one with _critical=true, the other ones having _critical=false with the associated column set to NULL
	 * @param $conditions Array of conditions for deleting (if count=0 then we look for the id of the object) 
	 * @param $seen Array of classes from were we deleted
	 * @param $recursive Bool default true. Whether to delete/clean objects pointing to this one 
	 * @return array(true/false,message) if the object(s) were deleted or not
	 */
	public function objDelete($conditions=array(), $seen=array(), $recursive=true)
	{
		$vars = self::getVariables(get_class($this));
		if (!$vars)
			return null;

		$orig_cond = $conditions;
		$table = $this->getTableName();
		if (!count($conditions)) {
			if ($this->isInvalid())
				return array(false, "Could not delete object of class ".get_class($this).". Object was previously invalidated.");
			
			if (($id_name = $this->GetIdName())) {
				$var = $this->variable($id_name);
				$id_value = $var->escape($this->{$id_name});
				if (!$id_value || $id_value == "NULL")
					$where = '';
				else {
					$where = " where \"$id_name\"=$id_value";
					$conditions[$id_name] = $id_value;
				}
			} else
				$where = '';
		} else
			$where = $this->makeWhereClause($conditions, true);
	//	Debug::output("entered objDelete ".get_class($this)." with conditions ".$where);

		if ($where == '') 
			return array(false, "Don't have any condition for deleting for object ".get_class($this));

		// array of pairs object_name=>array(array(var_name=>var_value),array(var_name2=>var_value2)) in which we have to check for deleting on cascade
		$to_delete = array();

		if ($recursive) {
			$objs = Model::selection(get_class($this),$conditions);
			for ($i=0; $i<count($objs); $i++) {
				foreach ($vars as $var_name=>$var) {
					$value = $objs[$i]->{$var_name};
					if (!$value)
						continue;
					//search inside the other objects if there are column that reference $var_name column
					foreach (self::$_models as $class_name=>$class_vars) {
						foreach ($class_vars as $class_var_name=>$class_var) {
							if (!($class_var->_key == $table && ($class_var_name == $var_name || $class_var->_matchkey == $var_name)))
								continue;

							$obj = new $class_name;
							$obj->{$class_var_name} = $value;
							if ($class_var->_critical) {
								// if relation is critical equivalent to delete on cascade, add $class_name to array of classes on which same method will be applied 
								if (!isset($to_delete[$class_name]))
									$to_delete[$class_name] = array(array($class_var_name=>$value));
								else
									$to_delete[$class_name][] = array($class_var_name=>$value);
							} else {
								// relation is not critical. we just need to set to NULL the fields pointing to this one
								$nr = $obj->fieldSelect('count(*)',array($class_var_name=>$value));
								if($nr) {
									//set column $class_var_name to NULL in all rows that have the value $value 
									$obj->{$class_var_name} = NULL;
									$obj->fieldUpdate(array($class_var_name=>$value),array($class_var_name));
								}
							}
						}
					}
				}
			}
		}
		$query = "DELETE FROM $table $where";
		$res = Database::query($query);
		$cnt = count($seen);
		array_push($seen,strtolower(get_class($this)));

		foreach ($to_delete as $object_name=>$conditions) {
			$obj = new $object_name;
			for ($i=0;$i<count($conditions);$i++)
				$obj->objDelete($conditions[$i],$seen);
		}
		if ($res) {
			if ($cnt) {
				self::writeLog("deleted ".$this->getNameInLogs()." $where","$query");
			} else
				return array(true, "Succesfully deleted ".pg_affected_rows($res)." object(s) of type ".get_class($this));
		} else {
			if ($cnt)
				Debug::output("Could not delete object of class ".get_class($this));
			else
				return array(false, "Could not delete object of class ".get_class($this));
		}
		return;
	}

	/**
	 * Get objects depending on the specified conditions from $dump_field from $table
	 * @param $objects_to_dump Array - Variable passed by reference where objects containing all found objects
		Ex: Array("extenion:2"=>The extension object with id 2, "group:3"=>The group object with id 3)
	 * @param $field_name Text - name of the field to use as condition
	 * @param $field_value Value of $field_name
	 * @param $table Text - name of the table where objects would point to
	 * @param $exceptions Array containing class names to skip 
	 * @param $depth_in_search Int default 0. The name of objects when this variable is 0 will added in returned array
	 * @return Array with pairs: class_name=>"name of the (first level) objects that will be dumped separated by ,"
	 */
	static function getObjectsToDump(&$objects_to_dump,$field_name, $field_value, $table, $exceptions=array(), $depth_in_search=0)
	{
		if (!self::$_models)
			self::init();
		if (!self::$_models)
			exit("Don't have modeles after init() was called.");

		$dump_description = array();

		// look at all the models in the system
		foreach (self::$_models as $class_name=>$model)
		{
			if (in_array($class_name, $exceptions))
				continue;
			if (!($use_dump_fields = self::matchesDumpConditions($model, $field_name, $field_value, $table)))
				continue;
			// get the objects in current class_name that match the dump_fields
			$objs = Model::selection($class_name, $use_dump_fields);
			$dump_desc = "";
			// for all found objects
			for ($i=0; $i<count($objs); $i++)
			{
				$obj = $objs[$i];
				$id_name = $obj->getIdName();
				if (!$id_name)
					continue;
				$id_value = $obj->{$id_name};

				$index = "$class_name:$id_value";

				// make sure we don't add the same object twice
				if (isset($objects_to_dump[$index]))
					continue;
				$objects_to_dump[$index] = $obj;
				if ($depth_in_search === 0) 
				{
					$name_var = '';
					if ($obj->variable($class_name))
						$name_var = $class_name;
					elseif ($obj->variable("name"))
						$name_var = "name";
					else
						$name_var = $id_name;
					if ($dump_desc != "")
						$dump_desc .= ", ";
//print "$name_var=". $obj->{$name_var}."<br/>";
					$dump_desc .= $obj->{$name_var};
//print $dump_desc."<br/>";
				}
//print "class=$class_name; $id_name=$id_value"."<br/>";
				self::getObjectsToDump($objects_to_dump, $id_name, $id_value, $obj->getTableName(), array_merge($exceptions,array($class_name)), $depth_in_search+1);
			}
			if ($dump_desc != "")
				$dump_description[self::getClassTableName($class_name)] = $dump_desc;
		}
		return $dump_description;
	}

	/**
	 * Verify if there are variables in provided $model that match $field_name from $table
	 * if yes then build conditions array for specied $model
	 * @param $model Array containing the variables of an object
	 * @param $field_name Text representing the name of the field where the variables from $model might point
	 * @param $field_value Value of $field_name. Will be used when building the return value
	 * @param $table Name of table from where $field_name is 
	 * @return false if no match Array defining the conditions to select objects
	 */
	static function matchesDumpConditions($model, $field_name, $field_value, $table)
	{
		$dump_fields = array();
		// or some other field might point to it (more than one field in the same object)
		foreach ($model as $var_name=>$var) 
		{
			if ($var->_key == $table && ($var_name==$field_name || $var->_matchkey==$field_name)) {
				if (!count($dump_fields))
					$dump_fields[$var_name] = $field_value;
				elseif (count($dump_fields)==1 && !isset($dump_fields[0])) {
					// if more than one field point to $field_name then build an OR condition between there fields
					$dump_fields = array(
						array_merge(
							array("__sql_relation"=>"OR",$var_name=>$field_value), 
							$dump_fields
						)
					);
				} else
					$dump_fields[0][$var_name] = $field_value;
				continue;
			}
		}
		if (!count($dump_fields))
			return false;
		return $dump_fields;
	}

	/**
	 * Recursive function that checks how many rows will be affected in the database, if objDelete will be called on this object using the same $conditions param.
	 * If table doesn't have references from other tables then this table will be the only one affected.
	 * @param $conditions Array of conditions for deleting (if count=0 then we look for the id of the object) 
	 * @param $message String representing the message 
	 * @return The message with the number affected row (deleted or set to NULL) in tables 
	 */
	public function ackDelete($conditions=array(), $message = "")
	{
		$vars = self::getVariables(get_class($this));
		if(!$vars)
			return null;

		$original_message = $message;
		$orig_cond = $conditions;
		$table = $this->getTableName();
		if(!count($conditions)) 
		{
			if($this->isInvalid())
				return array(false, "Could not try to delete object of class ".get_class($this).". Object was previously invalidated.");
			
			if(($id_name = $this->GetIdName()))
			{
				$var = $this->variable($id_name);
				$id_value = $var->escape($this->{$id_name});
				if(!$id_value || $id_value == "NULL")
					$where = '';
				else {
					$where = " where \"$id_name\"=$id_value";
					$conditions[$id_name] = $id_value;
				}
			}else
				$where = '';
		}else
			$where = $this->makeWhereClause($conditions, true);

		$objs = Model::selection(get_class($this),$conditions);
		// array of pairs object_name=>array(array(var_name=>var_value),array(var_name2=>var_value2)) in which we have to check for deleting on cascade
		$to_delete = array();
		for($i=0; $i<count($objs); $i++) 
		{
			foreach($vars as $var_name=>$var)
			{
				$value = $objs[$i]->{$var_name};
				if (!$value)
					continue;
				//search inside the other objects if there are column that reference $var_name column
				foreach(self::$_models as $class_name=>$class_vars)
				{
					foreach($class_vars as $class_var_name=>$class_var)
					{
						if (!($class_var->_key == $table && ($class_var_name == $var_name || $class_var->_matchkey == $var_name)))
							continue;

						$obj = new $class_name;
						$obj->{$class_var_name} = $value;
						if ($class_var->_critical)
						{
							// if relation is critical equivalent to delete on cascade, add $class_name to array of classes on which same method will be applied 
							if(!isset($to_delete[$class_name]))
								$to_delete[$class_name] = array(array($class_var_name=>$value));
							else
								$to_delete[$class_name][] = array($class_var_name=>$value);
						}
						else
						{
							$nr = $obj->fieldSelect('count(*)',array($class_var_name=>$value));
							if($nr)
								$message .= ", ".$obj->getTableName();
						}
					}
				}
			}
		}
		$message .= ", ".$this->getTableName();
 
		foreach($to_delete as $object_name=>$conditions)
		{
			$obj = new $object_name;
			for($i=0;$i<count($conditions);$i++)
				$message .= $obj->ackDelete($conditions[$i]);
		}

		return $message;
	}

	/**
	 * Extend the calling object with the variables provided
	 * @param $vars Array of pairs : $var_name=>$table_name that will be added to this objects model
	 * If $var_name = "var_name_in_calling_table:referenced_var_name" then the new variable will be called  var_name_in_calling_table and will point to referenced_var_name in $table_name
	 */
	public function extend($vars)
	{
		foreach($vars as $var_name=>$table_name) 
		{
			$break_var_name = explode(":",$var_name);
			if(count($break_var_name) == "2") 
			{
				$var_name = $break_var_name[0];
				$references = $break_var_name[1];
			}else
				$references = NULL;

			if(isset($this->_model[$var_name]))
			{
				self::warning("Trying to override existing variable $var_name. Ignoring this field when extending.");
				continue;
			}
			// don't let user extend the object using a numeric key
			if(is_numeric($var_name))
			{
				exit("$var_name is not a valid variable name. Please do not use numbers or numeric strings as names for variables.");
			}
			if(!is_array($table_name))
				$this->_model[$var_name] = new Variable("text",null,$table_name,false,$references);
			else{
				$this->_model[$var_name] = new Variable("text",null,$table_name["table"],false,$references,$table_name["join"]);}
			$this->{$var_name} = NULL;
		}
	}

	/**
	 * Merge two objects (that have a single field common, usually an id)
	 * Function was done to make easier using 1-1 relations
	 * @param $object is the object whose properties will be merged with those of the calling object: $this
	 */
	public function merge($object)
	{
		$party_vars = self::getVariables(get_class($object));
		$vars = self::getVariables(get_class($this));
		$party_table = $object->getTableName();

		foreach($party_vars as $var_name=>$value)
		{
			// fields that are named the same as one of the calling object will be ignored
			// for fields that have the same name in both objects please use the extend function : extend(array("key_in_original_table:rename_key_for_calling_table"=>"original_table"));
			if(isset($vars[$var_name]))
				continue;
			$this->_model[$var_name] = new Variable("text",null,$party_table);
		}
	}


	/**
	 * Sets the value of each variable inside this object to NULL
	 */
	public function nulify()
	{
		$vars = self::getVariables(get_class($this));
		foreach($vars as $var_name=>$var)
			$this->{$var_name} = NULL;
	}

	/**
	 * Exports an array of objects to an array of array.
	 * @param $objects Array of objects to be exported
	 * @param $formats Array of pairs $var_name(s)=>$value
	 * $var_name is a name of a variable or more variable names separated by commas ','
	 * $value can be '' if column will be added in the array with the same name and the value resulted from query 
	 * $value can be 'function_nameOfFunction' the $nameOfFunction() will be applied on the value resulted from query  and that result will be added in the array to be returned
	 * $value can be 'name_to_appear_under' if  column will be added in the array with the name name_to_appear_under and the value resulted from query 
	 * $value can be 'function_nameOfFunction:name_to_appear_under' in order to have name_to_appear_under and value returned from calling the function nameOfFunction
	 * The most complex usage is: $var_name is 'var1,var2..' and $value is 'function_nameOfFunction:name_to_appear_under', then nameOfFunction(var1,var2,..) will be called and the result will be added in the array to be returned under name_to_appear_under
	 * $var_name can start with ('1_', '2_' ,'3_' , '4_', '5_', '6_', '7_', '8_', '9_', '0_'), that will be stripped in order to have 
	 * multiple fields in the array generated from the same $variable 
	 * @param $block Bool value. If true then only the fields specified in $formats will be returned
	 */
	public static function objectsToArray($objects, $formats, $block = false)
	{
		if (!count($objects))
			return array();
		// array of beginnings that will be stripped
		// usage is motivated by need of having two columns in the array generated from a single variable
		// Example: we have a timestamp field called date, but we need two fields in the array, one for date and the other for time
		// $formats=array("1_date"=>"function_get_date:date", "2_date"=>"function_get_time:time")
		$begginings = array('1_', '2_' ,'3_' , '4_', '5_', '6_', '7_', '8_', '9_', '0_');
		$i = 0;
		if (!isset($objects[$i])) {
			while(!isset($objects[$i])) {
				$i++;
				if ($i>200) 
				{
//					print "<br/>\n<br/>\nInfinit loop<br/>\n<br/>\n";
					Debug::output("Infinit loop");
					return;
				}
			}	
		}
		$vars = $objects[$i]->_model;
		if(!$vars)
			return null;
		$array = array();

		if(count($objects))
			$id =$objects[0]->getIdName(); 
		for($i=0; $i<count($objects); $i++) 
		{
			if(!isset($objects[$i]))
				continue;
			$vars = $objects[$i]->_model;
			$keep = array();
			if ($formats != 'all')
				foreach($formats as $key=>$value)
				{
					$key = trim($key);
					if(in_array(substr($key,0,2), $begginings))
						$key = substr($key,2,strlen($key));
					$name = ($value == '') ? $key : $value;
					if(substr($name,0,9) == "function_") 
					{
						$name = substr($name,9,strlen($name));
						$arr = explode(':',$name);
						if(count($arr)>1)
						{
							$newname = $arr[1];
							$name = $arr[0];
						}else
							$newname = $key;
						if(str_replace(',','',$key) == $key)
							$array[$i]["$newname"] = call_user_func($name,$objects[$i]->{$key});
						else
						{
							$key = explode(',',$key);
							$params = array();
							for($x=0; $x<count($key); $x++)
								$params[trim($key[$x])] = $objects[$i]->{trim($key[$x])};
							$array[$i]["$newname"] = call_user_func_array($name,$params);
							$key = implode(":",$key);
						}
					}else
						$array[$i]["$name"] = $objects[$i]->{$key};
					$keep[$key] = true;
				}
			//by default if $block is not true then the id of this object will be added to the result
			if (!$block) 
			{
				foreach($vars as $key=>$value)
				{
					if ($formats != 'all' && $key!=$id)
						continue;
					$array[$i]["$key"] = $objects[$i]->{$key};
					$keep[$key] = true;
				}
			}
		}
		return $array;
	}

	/**
	 * Perform vacuum on this object's associated table
	 */
	public function vacuum()
	{
		$table = $this->getTableName();
		$query = "VACUUM ANALYZE $table";
		Database::query($query);
	}

	/**
	* Convert a boolean or SQL bool representation to a SQL bool
	* @param $value Value to convert, can be true, false, 't' or 'f'
	* @param $defval Default to return if $value doesn't match
	*/
	public static function sqlBool($value, $defval = 'NULL')
	{
		if (($value === true) || ($value === 't'))
			return 't';
		if (($value === false) || ($value === 'f'))
			return 'f';
		return $defval;
	}

	/**
	 * Creates a WHERE clause for a query
	 * @param $conditions Array defining the condtions
	 * @return Text representing the WHERE clause
	 */
	public function exportWhereClause($conditions)
	{
		return $this->makeWhereClause($conditions);
	}

	/**
	 * Return whether the model was modified or not
	 */
	public static function modified()
	{
		return self::$_modified;
	}

	/**
	 * Get a list of all classes derived from Model
	 * @return Array of strings that represent the names of Model classes
	 */
	static function getModels()
	{
		$models = array();
		$classes = get_declared_classes();
		foreach ($classes as $class)
		{
			if (get_parent_class($class) == "Model" || get_parent_class(get_parent_class($class)) == "Model")
				$models[] = $class;
		}
		return $models;
	}

	/**
	 * One-time initialization of the static array of model variables.
	 * This method is called internally from any methods that need access
	 *  to the variables of any derived class.
	 * IMPORTANT: All derived classes must be defined when this method is
	 *  called for the first time.
	 */
	static function init()
	{
		if (self::$_models)
			return;
		$classes = get_declared_classes();
		foreach ($classes as $class)
		{
			// calling static class methods is done using an array("class","method")		
			if (get_parent_class($class) == "Model" || get_parent_class(get_parent_class($class)) == "Model")
			{
				$vars = null;
				$vars = @call_user_func(array($class,"variables"));
				if (!$vars)
					continue;
				foreach ($vars as &$var)
					$var->_owner = $class;
				self::$_models[strtolower($class)] = $vars;
				$obj = new $class;
				// check to see if this object is a performer for the ActionLog class
				$performer = $obj->isPerformer();
				if($performer && count($performer))
					self::$_performers[strtolower($class)] = $performer;
			}
		}
	}

	/**
	 * Update the database to match all the models
	 * @return True if the database was synchronized with all the models
	 */
	static function updateAll()
	{
		if (!Database::connect())
			return false;
		self::init();
		foreach (self::$_models as $class => $vars)
		{
			$object = new $class;
			$table = $object->getTableName();
			if (!Database::updateTable($table,$vars))
			{
				self::warning("Could not update table of class $class\n");
				return false;
			}
			else
				self::$_modified = true;

			if (!method_exists($object,"index"))
				continue;
			if ($index = call_user_func(array($class,"index")))
				Database::createIndex($table,$index);
		}
		if(self::$_modified)
			foreach(self::$_models as $class => $vars) {
				$object = new $class;
				if(method_exists($object, "defaultObject"))
					$res = call_user_func(array($object,"defaultObject"));
			}
		return true;
	}

	/**
	 * Get the database mapped variables of a Model derived class
	 * @param $class Name of class whose variables will be described
	 * @return Array of objects of type Variable that describe the
	 *  database mapped variables of any of the @ref $class objects
	 */
	public static function getVariables($class)
	{
		self::init();
		$class = strtolower($class);
		if (isset(self::$_models[$class]))
			return self::$_models[$class];
		return null;
	}

	/**
	 * Get the Variable object with the name specified by $name from class $class, if valid variable name in class
	 * @param $class Name of the class
	 * @param $name Name of the variable in the object
	 * @return Object of type Variable or null if not found
	 */
	public static function getVariable($class,$name)
	{
		$vars = self::getVariables($class);
		if (!$vars)
			return null;
		return isset($vars[$name]) ? $vars[$name] : null;
	}

	/**
	 * Gets the variables of a certian object(including thoses that were added using the extend function)
	 * @return Array of objects of type Variable that describe the current extended object
	 */
	public function extendedVariables()
	{
		return $this->_model;
	}

	/**
	 * Returns the variable with the specified name or NULL if variable is not in the object
	 * @param $name Name of the variable
	 * @return Variable object or NULL is variable is not defined
	 */
	public function variable($name)
	{
		return isset($this->_model[$name]) ? $this->_model[$name] : null;
	}

	/**
	 * Get the name of a table corresponding to this object. Method can be overwrited from derived class when other name for the table is desired. 
	 * @return Name of table corresponding to $this object
	 */
	public function getTableName()
	{
		$class = strtolower(get_class($this));
		if(substr($class,-1) != "y")
			return $class . "s";
		else
			return substr($class,0,strlen($class)-1) . 'ies';
	}

	/**
	 * Get the name of the table associated to the given class
	 * @param $class Name of the class to get the table for
	 * @return Table name 
	 */
	public static function getClassTableName($class)
	{
		if(!isset(self::$_models[strtolower($class)]))
			return null;

		$obj = new $class;
		return $obj->getTableName();
	}

	/**
	 * Get an object by giving the name of the sql table
	 * @param $table Name of table in sql
	 * @return Object or NULL
	 */
	public static function getObject($table)
	{
		if(!$table)
			return NULL;

		foreach(self::$_models as $class=>$vars)
		{
			if(self::getClassTableName($class) == $table)
				return new $class;
		}

		return NULL;
	}

	/**
	 * Print warning if warnings where setted as enabled in $_SESSION
	 * @param $warn String representing the warning
	 */
	public static function warning($warn)
	{
		if(isset($_SESSION["warning_on"]))
//			print "<br/>\nWarning : $warn<br/>\n";
			Debug::output("Warning : $warn");
	}

	/**
	 * Print notice if notices were enabled in $_SESSION
	 * @param $note The notice to be printed
	 */
	public static function notice($note)
	{
		if(isset($_SESSION["notice_on"]))
//			print "<br/>\nNotice : $note<br/>\n";
			Debug::output("Notice : $note");
	}

	/**
	 * Checks if $name is a valid column inside the specified table
	 * @param $column_name Name of column(variable) to check
	 * @param $table Name of table
	 * @return BOOL value: true if $table is associated to an object and $column_name is a valid variable for that object, false otherwise
	 */
	protected static function inTable($column_name, $table)
	{
		if(!($obj = self::getObject($table)))
			return false;
		if($obj->variable($column_name))
			return true;

		return false;
	}

	/**
	 * Get the name of the variable representing the numeric id for this object
	 * @return Name of id variable or NULL if object was defined without a numeric id
	 */
	public function getIdName()
	{
		$vars = self::getVariables(get_class($this));
		foreach($vars as $name => $var)
		{
			//the id of a table can only be serial or bigserial
			if($var->_type != "serial" && $var->_type != "bigserial")
				continue;
			//if it's a foreign key to another table,we ignore that it was defined and serial or bigserial
			if($var->_key && $var->_key != '')
				continue;
			return $name;
		}
		//it might be possible that the object was defined without a numeric id
		return NULL;
	}

	/**
	 * Invalidate object. Object can't be used for generating WHERE clause for DELETE or UPDATE statements
	 */
	protected function invalidate()
	{
		self::warning("Invalidating object: ".get_class($this).".");
		$this->_invalid = true;
	}

	/**
	 * Checks to see if an object is invalid(can't be used to generated WHERE clause for DELETE or UPDATE statements)
	 * @return Bool: true is object is invalid, false otherwise
	 */
	protected function isInvalid()
	{
		return $this->_invalid;
	}

	/**
	 * Creates SELECT statement from given clauses
	 * @param $columns String representing what to select
	 * @param $from_clause String telling where to select from
	 * @param $where_clause String with conditions
	 * @param $order String/Array of pairs of field=>"ASC"/"DESC" defining order
	 * @param $limit Number representing the maximum number of fields to be selected
	 * @param $offset Number representing the offset to be used in the query
	 */
	protected static function buildSelect($columns, $from_clause=NULL, $where_clause=NULL, $order=NULL, $limit=NULL, $offset=0, $group_by = NULL, $having = NULL)
	{
		$ord = self::makeOrderClause($order);
		$order_clause = ($ord) ? " ORDER BY $ord" : NULL;
		$limit_clause = ($limit) ? " LIMIT $limit" : NULL;
		$offset_clause = ($offset) ? " OFFSET $offset" : NULL;
		$group_by = ($group_by) ? "GROUP BY $group_by" : NULL;
		$having = ($having) ? " HAVING $having" : NULL;

		$query = "SELECT $columns FROM $from_clause $where_clause $group_by $having $order_clause $limit_clause $offset_clause";
		return $query;
	}

	/**
	 * Returns a WHERE clause for a query
	 * @param $conditions Array defining the conditions for a query
	 * Array is formed by pairs of $key=>$value. $value can also be an array
	 * Ex to buid AND : "date"=>array(">2008-07-07 00:00:00", "<2008-07-07 12:00:00") means WHERE date>'2008-07-07 00:00:00' AND date<'2008-07-07 12:00:00'
	 * EX to build OR : ("date"=>"<2008-07-07 00:00:00", "date"=>">2008-07-07 12:00:00") means WHERE date<'2008-07-07 00:00:00' OR date>'2008-07-07 12:00:00'
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred
	 * Value is true when method is called from within a method that never returns extended objects.
	 * @param $without_table 
	 * @param $null_exception Enables a verification 
	 * @return Text representing the WHERE clause or '' if the count($conditions) is 0
	 */
	protected function makeWhereClause($conditions, $only_one_table = false, $without_table = false)
	{
		$where = ' WHERE ';
		if(!count($conditions))
			return '';
		$obj_table = $this->getTableName();
		foreach($conditions as $key=>$value)
		{
			if ($value === NULL)
				continue;

			if ($where != " WHERE ")
				$where .= " AND ";
/*
			// old implementation. I will keep for some time
			if(is_array($value) && is_numeric($key))
				$clause = $this->buildOR($value, $obj_table, $only_one_table, $without_table);
			elseif(is_array($value)) {
				$clause = $this->buildAND($key, $value, $obj_table, $only_one_table, $without_table);
			} else
				$clause = $this->makeCondition($key, $value, $obj_table, $only_one_table, $without_table);
*/

			if (is_array($value))
				$clause = $this->buildAND_OR($key, $value, $obj_table, $only_one_table, $without_table);
			else
				$clause = $this->makeCondition($key, $value, $obj_table, $only_one_table, $without_table);

			$where .= $clause;
		}
		if($where == " WHERE ")
			return '';
		return $where;
	}

	/**
	 *	Builds AND/OR subcondition
	 * @param $key name of the column on which the conditions are set
	 * @param $value Array with the allowed values for the $key field
	 * @param $obj_table Name of the table associated to the object on which method is called
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred
	 * Value is true when method is called from within a method that never returns extended objects.
	 * @param $without_table The name of the tables won't be specified in the query: Ex: we won't have table_name.column, just column
	 */
	protected function buildAND_OR($key, $value, $obj_table, $only_one_table, $without_table)
	{
		// this is an OR or an AND
		$sql_rel = (isset($value["__sql_relation"])) ? $value["__sql_relation"] : "AND";
		if($sql_rel == "AND")
			$clause = "(".$this->buildAND($key, $value, $obj_table, $only_one_table, $without_table).")";
		else
			$clause = $this->buildOR($key, $value, $obj_table, $only_one_table, $without_table);
		return $clause;
	}

	/**
	 * Build part of a WHERE clause (conditions will be linked by AND)
	 * @param $key name of the column on which the conditions are set
	 * @param $allowed_values Array with the allowed values for the $key field
	 * @param $obj_table Name of the table associated to the object on which method is called
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred
	 * Value is true when method is called from within a method that never returns extended objects.
	 * @param $without_table The name of the tables won't be specified in the query: Ex: we won't have table_name.column, just column
	 */
	protected function buildAND($key, $allowed_values, $obj_table, $only_one_table = false, $without_table = false)
	{
		$t_k = $this->getColumnName($key, $obj_table, $only_one_table, $without_table);

		$clause = "";
//		for($i=0; $i<count($allowed_values); $i++)
//		{
//			if($clause != "")
//				$clause .= " AND "; 
//			$clause .= $this->makeCondition($t_k, $allowed_values[$i], $obj_table, $only_one_table, true);
//		}
		foreach($allowed_values as $var_name=>$var_value)
		{
			if($var_name === "__sql_relation")
				continue;

			if($clause != "")
				$clause .= " AND "; 

			if(is_array($var_value)) {
				$clause .= $this->buildAND_OR($var_name, $var_value, $obj_table, $only_one_table, $without_table);
				continue;
			}
			elseif(is_numeric($var_name))
				$t_k = $this->getColumnName($key, $obj_table, $only_one_table, $without_table);
			else
				$t_k = $this->getColumnName($var_name, $obj_table, $only_one_table, $without_table);

			$clause .= $this->makeCondition($t_k, $var_value, $obj_table, $only_one_table, true);
		}
		return $clause;
	}

	/**
	 * Build part of a WHERE clause (conditions will be linked by AND)
	 * @param $conditions Array of type $key=>$value representing the clauses that will be separated by OR
	 * @param $obj_table Name of the table associated to the object on which method is called
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred
	 * Value is true when method is called from within a method that never returns extended objects.
	 * @param $without_table The name of the tables won't be specified in the query: Ex: we won't have table_name.column, just column
	 */
	protected function buildOR($key, $conditions, $obj_table, $only_one_table = false, $without_table = false)
	{
		$clause = "";
		foreach($conditions as $column_name=>$value)
		{
			if($column_name === "__sql_relation")
				continue;
			if($clause != "")
				$clause .= " OR "; 
			if(is_array($value)) {
				$clause .= $this->buildAND_OR($column_name, $value, $obj_table, $only_one_table, $without_table);
				continue;
			}
			if(is_numeric($column_name))
				$t_k = $this->getColumnName($key, $obj_table, $only_one_table, $without_table);
			else
				$t_k = $this->getColumnName($column_name, $obj_table, $only_one_table, $without_table);
			$clause .= $this->makeCondition($t_k, $value, $obj_table, $only_one_table, true);
		}
		return " (" . $clause. ") ";
	}

	/**
	 * Return the name of a column in form "table_name"."column" that will be used inside a query
	 * @param $key Name of the column
	 * @param $obj_table Table associated to $this object
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred(if true, "table_name" won't be added)
	 * @param $without_table Bool value, if true "table_name" won't be specified automatically (it might be that it was already specified in the $key)
	 */
	protected function getColumnName($key, $obj_table, $only_one_table, $without_table)
	{
		if(!$without_table) 
		{
			// If $key starts with "__sql_" then use of function inside the clause is allowed.
			// Example: $key can be date(tablename.timestamp_field) or length(tablename.text_field)
			// Developer has the responsibility to add the name of the table if necessary and to add "" 
			// in case reserved words in PostgreSQL were used as column names or table names
			if (substr($key,0,6) == "__sql_")
				$t_k = substr($key,6,strlen($key));
			else
			{ 
				$look_for_other_table = true;
				//if we use only one table and $key is a variable inside this object
				if($only_one_table)
				{
					$var = self::getVariable(get_class($this), $key);
					//this condition should always be valid, if methods were used in the right way
					//if condition won't be verified because this object was extended and a method for objects that 
					//were not extended was called WHERE clause will be correct but query will most likely fail in
					//the FROM section
					if($var)
					{
						$table = $obj_table;
						$look_for_other_table = false;
					}
				}
				if($look_for_other_table)
				{
					$var = $this->_model[$key];
					$table = $var->_key;
					$matchkey = $var->_matchkey;
					//if matchkey is not specified 
					if(!$table || $table == '')
						$table = $obj_table;
					if(!Model::getVariable(get_class($this),$key)) {

					/*  ex: status field is in the both classes and i put condition on the field that was inserted with method extend*/
					if($table != $obj_table && $matchkey)
						$key = $matchkey;
					}else
						$table = $obj_table;
				}
				$t_k = "\"$table\".\"$key\"";
			}
		}else{
			if (substr($key,0,6) == "__sql_")
				$t_k = substr($key,6,strlen($key));
			else
				$t_k = "$key";
		}

		return $t_k;
	}

	/**
	 * Build a condition like table_name.column='$value' or table_name.column>'$value'
	 * @param $key Represents the table_name.column part of the condition
	 * @param $value String representing the operator and the value, or just the value when then default operator = will be used
	 * @param $obj_table Table associated to $this object
	 * @param $only_one_table Bool value specifing if inside the query only one table is referred(if true, "table_name" won't be added)
	 * @param $without_table Bool value, if true "table_name" won't be specified automatically (it might be that it was already specified in the $key)
	 */
	protected function makeCondition($key, $value, $obj_table, $only_one_table = false, $without_table = false)
	{
		$t_k = $this->getColumnName($key, $obj_table, $only_one_table, $without_table);
		// Arrays of operators that should be put at the beggining in $value 
		// If none of this operators is used and $value does not have a special value then 
		// the default operator is =
		$two_dig_operators = array("<=",">=","!=");
		$one_dig_operators = array(">","<","=");

		$first_two = substr($value,0,2);
		$first_one = substr($value,0,1);
		$clause = '';

		if ($value === false)
			$clause .= " $t_k IS NOT TRUE ";
		elseif($value === true)
			$clause .= " $t_k IS TRUE ";
		elseif($value === "__empty")
			$clause .= " $t_k IS NULL ";
		elseif($value === "__non_empty" || $value === "__not_empty")
			$clause .= " $t_k IS NOT NULL ";
		elseif(in_array($first_two, $two_dig_operators)){
			$value = substr($value,2,strlen($value));
			if (substr($value,0,6) == "__sql_")
			{
				// If $value starts with "sql_" then $value is not actually a value but 
				// refers to a column from a table
				$value = substr($value, 6, strlen($value));
				$clause .= " $t_k" . $first_two . "$value ";
			}else{
				$value = Database::escape($value);
				$clause .= " $t_k" . $first_two . "'$value' ";
			}
		}elseif (in_array($first_one, $one_dig_operators)) {
			$value = substr($value,1,strlen($value));
			if (substr($value,0,6) == "__sql_")
			{
				$value = substr($value, 6, strlen($value));
				$clause .= " $t_k" . $first_one . "$value ";
			}else{
				$value = Database::escape($value);
				$clause .= " $t_k" . $first_one . "'$value' ";
			}
		}elseif (substr($value,0,6) == "__LIKE") {
			$value = Database::escape(substr($value,6,strlen($value)));
			if (substr($value,0,1) != '%' && substr($value,-1) != '%')
				$clause .= " $t_k ILIKE '$value%' ";
			else
				$clause .= " $t_k ILIKE '$value' ";
		}elseif (substr($value,0,10) == "__NOT LIKE") {
			$value = Database::escape(substr($value,10,strlen($value)));
			if (substr($value,0,1) != '%' && substr($value,-1) != '%')
				$clause .= " $t_k NOT ILIKE '$value%' ";
			else
				$clause .= " $t_k NOT ILIKE '$value' ";
		}elseif(substr($value,0,6) == "__sql_") {
			$value = substr($value,6,strlen($value));
			$clause .= " $t_k=$value";
		}else{
			if ($value != '' && strlen($value))
				$clause .= " $t_k='".Database::escape($value)."'";
			else
				// it should never get here
				// if verification for NULL is needed set $value = '__empty' 
				$clause .= " $t_k is NULL";
		}
		return $clause;
	}

	/**
	 * Creates an ORDER clause
	 * @param $order Array for building clause array("name"=>"DESC", "created_on"=>"ASC") or String with 
	 * clause already inserted "name DESC, created_on"
	 * string can also be "rand()", for getting the results in random order
	 * @return ORDER clause 
	 */
	protected static function makeOrderClause($order)
	{
		// When writing the String one must pay attention to use "" for fields and tables that are in
		// the special words in PostgreSQL
		if(!count($order))
			return;
		if (!is_array($order))
			return $order;
		$clause = '';
		foreach($order as $key=>$value) 
		{
			if ($clause != '')
				$clause .= ',';
			if ($value == "DESC")
			{
				if (substr($key,0,1) == "\"")
					$clause .= " $key $value";
				else
					$clause .= " \"$key\" $value";
			}else{
				if (substr($key,0,1) == "\"")
					$clause .= " $key";
				else
					$clause .= " \"$key\"";
			}
		}
		return $clause;
	}

	/**
	 * Adding an inner query to a WHERE clause
	 * @param $inner_query Array of params for defining the inner query
	 * @param $table Table to use for the column on which the inner query is applied
	 * @param $where Clause to append to 
	 * @return WHERE clause
	 */
	protected function makeInnerQuery($inner_query=array(), $table = NULL, $where='')
	{
		if(!is_array($inner_query) || !count($inner_query))
			return $where;

		if(!$table || $table == '')
			$table = $this->getTableName();

		// Verifying the compulsory $keys 
		$compulsory = array("column", "relation");
		$error = '';
		for($i=0; $i<count($compulsory); $i++)
		{
			if (!isset($inner_query[$compulsory[$i]]))
				$error .= 'Field '.$compulsory[$i].' is not defined. ';
		}
		if ($error != '')
			exit($error);

		if ($where == '')
			$where = ' WHERE ';
		else
			$where .= ' AND ';

		if (isset($inner_query['table']))
			$table = $inner_query['table'];
		$column = $inner_query["column"];
		$relation = $inner_query["relation"];
		$outer_column = $this->getColumnName($column,$table,false,false);
		if (!isset($inner_query["options"])) {
			if (!isset($inner_query["other_table"]) && !isset($inner_query["inner_table"]))
				exit("You must either insert 'other_table' or 'inner_table'");

			$inner_table = (isset($inner_query["inner_table"])) ? $inner_query["inner_table"] : $inner_query["other_table"];
			$inner_column = (isset($inner_query["inner_column"])) ? $inner_query["inner_column"] : $inner_query["column"];
			$inner_column = $this->getColumnName($inner_column, $inner_table, false, true);
			$where .= " $outer_column $relation (SELECT $inner_column from \"$inner_table\" ";
			$inner_where = '';

			if(!($obj = self::getObject($inner_table)))
				exit("Quit when wanting to create object from table $inner_table");

			if(isset($inner_query["conditions"]))
				$inner_where .= $obj->makeWhereClause($inner_query["conditions"],true);

			if(isset($inner_query["inner_query"]))
				$inner_where .=$obj->makeInnerQuery($inner_query["inner_query"]);

			$group_by = (isset($inner_query['group_by'])) ? 'group by '.$inner_query['group_by'] : '';
			$having = (isset($inner_query['having'])) ? 'having '.$inner_query['having'] : '';
			$where .= $inner_where ." $group_by $having )";
		}else
			$where .= " $outer_column $relation (".$inner_query["options"].")";
		
		return $where;
	}

	/**
	 * Populates the variables of $this object with the fields from a query result
	 * @param $result Query result
	 */
	protected function populateObject($result)
	{
		if(pg_num_rows($result) != 1)
		{
			self::warning("Trying to build single object from sql that has ".pg_num_rows($result)." rows. Invalidating object.");
			$this->invalidate();
			return;
		}
		$this->_retrieved = true;
		$allow_html  = $this->allowHTML();
		foreach(pg_fetch_array($result,0) as $var_name=>$value) {
			$this->{$var_name} = Database::unescape($value);
			if(!in_array($var_name, $allow_html))
				$this->{$var_name} = htmlentities($this->{$var_name});
		}
	}

	/**
	 * Builds an array of objects that have the same variables as $this object from a result of a query
	 * @param $result Query result to build objects from
	 * @return Array of objects
	 */
	protected function buildArrayOfObjects($result)
	{
		if(!pg_num_rows($result))
			return array();

		$objects = array();
		$allow_html  = $this->allowHTML();
		//get the name of the class of $this object
		$class_name = get_class($this);
		for($i=0; $i<pg_num_rows($result); $i++) {
			// create a clone of $this object, not just having the same class, but also the same variables
			// (in case $this object was extended previously)
			$clone = new $class_name;
			$clone->_model = $this->_model;
			foreach(pg_fetch_array($result,$i) as $var_name=>$value) {
				$clone->{$var_name} = Database::unescape($value);
				if(!in_array($var_name, $allow_html))
					$clone->{$var_name} = htmlentities($clone->{$var_name});
			}
			$objects[$i] = $clone;
			$objects[$i]->_retrieved = true;
		} 
		return $objects;
	}

	/**
	 * Specify the name of the columns that allow html to be stored (HTML can be stored, but it be passed thought htmlentities when setting the values of the columns)
	 * This function must be reimplemented in each object that allows this type of columns
	 * @return Array contining the name of the columns or empty array
	 */
	protected function allowHTML()
	{
		return array();
	}

	/**
	 *	Build a string from all the variables of the objects: "var1=value1 var2=value2 ..."
	 * @param $prefix String representing a prefix that will be added in front of every var: "$prefix"."var1=value1 "."$prefix"."var2=value2 ..."
	 * @param $skip Array with name of variables to be skipped when building the string
	 * @return String of type:  "var1=value1 var2=value2 ..."
	 */
	public function toString($prefix = '', $skip=array())
	{
		$model = $this->_model;
		$str = "";
		foreach($model as $var_name=>$var)
		{
			if(in_array($var_name, $skip))
				continue;
			if($str != "")
				$str .= " ";
			$name = ($prefix != '') ? $prefix.".".$var_name : $var_name;
			$str .= "$name=".Model::$this->{$var_name};
		}
		return $str;
	}

	/**
	 *	Get name of this object
	 * @return String representing the name of the object. If all letters in class name are uppercase then they are returned the same, else all letters are lowercased
	 */
	public function getObjectName()
	{
		$class_name = get_class($this);
		if(strtoupper($class_name) == $class_name)
			return str_replace("_"," ",$class_name);
		else
			return strtolower(str_replace("_"," ",$class_name));
	}

	/**
	 * Escape spaces from given parameter $val and remove new lines
	 * @param $val Value to escape
	 * @return String representing the escaped value
	 */
	public static function escapeSpace($val)
	{
		$val = str_replace("\n", "", $val); //make sure new lines are not accepted
		return str_replace(" ", "\ ", $val);
	}

	/**
	 * Write a log entry in the database coresponding to a certain operation
	 * Note!! Only insert, delete, update queries are logged
	 * Other operations should be implemented in the classes or directly in the code
	 * The actual log writting is implemented in class ActionLog. If this class is not present logs are not written
	 */
	static function writeLog($log, $query = NULL)
	{
		global $enable_logging;

		if($enable_logging !== true && $enable_logging != "yes" && $enable_logging != "on")
			return;

		if(!self::getVariables("actionlog"))
			return;

		// if class ActionLog is present trying writting log
		ActionLog::writeLog($log, $query);
	}

	

	/**
	 * Verify if an object is a performer or not
	 * This function should be reimplemented in the classes that you wish to mark as performers
	 * Example: for class User, function should return array("performer_id"=>"user_id", "performer"=>"username"), "performer_id" and "performer" are constants and their values will be taken from the coresponding variables kept in $_SESSION: user_id and "username"
	 * @return Bool false
	 */
	protected function isPerformer()
	{
		return false;
	}

	/**
	 * Get the name of the object that should be used when writing logs
	 * This function returns the class of the object. If other name is desired one should reimplement it in the derived classes
	 * @return Name to be used when writing logs for this object 
	 */
	public function getNameInLogs()
	{
		return get_class($this);
	}
}

?>