<?php

namespace Sidus\Properties;

interface propertyInterface{

	protected $value;
	protected $has_changed = false;
	protected $type;
	
	/**
	 * Set the value for the first time where $value is a correct PHP type
	 * @param $value
	 */
	public function __constructor($value);

	/**
	 * Return the value for PHP with correct type and no processing for display
	 * @return mixed
	 */
	public function get();

	/**
	 * Set the value of the property where $value is a correct PHP type
	 * Must change $this->has_changed to true;
	 * @param $value
	 * @return boolean
	 */
	public function set($value);

	/**
	 * Check if the value has been changed since it's first assignment
	 * @return boolean
	 */
	public function hasChanged();
	
	/**
	 * Reset the hasChanged property to false
	 */
	public function reset();

	/**
	 * Return a string ready to be inserted in a SQL query, be sure to escape
	 * special characters properly !
	 * If the database type is not known, there MUST be a fall back with a
	 * standard SQL92 output.
	 * @param \SQL\Database $db handler to database 
	 * @return string
	 */
	public function toDB(\SQL\Database $db);

	/**
	 * Check if the $value is acceptable for this property
	 * @param $value
	 * @return boolean
	 */
	public function check($value);

	/**
	 * Return the SQL type of the property
	 * If the database type is not known, there MUST be a fall back with a
	 * standard SQL92 type.
	 * @param $db_type = mysql | sqlite3 | postgres | oracle ...
	 * @return string
	 */
	//public function getSqlType($db_type);

	/**
	 * Return a primitive PHP type or a class name as a string.
	 * @return string
	 */
	//public function getPhpType();
	
}

?>
