<?php

namespace Sidus\Properties;

interface propertyInterface{

	protected $value;
	protected $has_changed = false;
	protected $table_name;
	protected $column_name;
	protected $pdo_param;
	protected $model_name;
	protected $input;
	
	/**
	 * Set the value for the first time where $value is a correct PHP type
	 * @see http://php.net/manual/en/pdo.constants.php Documentation for PDO Params
	 * @param string $table_name Table name of the property in the Database
	 * @param string $column_name Column name of the property in the Database
	 * @param integer $pdo_type The PDO param type of the value in the DB
	 * @param string $model_name If different from column name, the PHP name
	 */
	public function __constructor($table_name, $column_name, $pdo_param = \PDO::PARAM_STR, $model_name = null);

	/**
	 * Return the value for PHP with correct type and no processing for display
	 * @return mixed $value
	 */
	public function get();

	/**
	 * Set the value of the property where $value is a correct PHP type
	 * Must change $this->has_changed to true;
	 * @param mixed $value
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
	 * @return null
	 */
	public function reset();

	/**
	 * Return a variable for binding params to prepared statements
	 * @return mixed $value
	 */
	public function toDB();

	/**
	 * Check if the $value is acceptable for this property
	 * @param $value
	 * @return boolean
	 */
	public function check($value);

	/**
	 * The default output formatting of the variable
	 * @return string $value 
	 */
	public function __toString();
	
	/**
	 * Returns the table name of the property
	 * @return string $table_name 
	 */
	public function getTableName();
	
	/**
	 * Returns the column name of the property
	 * @return string $column_name 
	 */
	public function getColumnName();
	
	/**
	 * Returns the model name of the property, $column_name by default
	 * @return string $mode_name 
	 */
	public function getModelName();
	
	/**
	 * Returns the PDO parameter type of the property
	 * @return string $value 
	 */
	public function getPDOParam();
	
	/**
	 * Returns the default input object for forms for this property
	 * @return string $value 
	 */
	public function getInput();
	
	/**
	 * Set the default input object for forms for this property
	 * @param \HTML\Input $input 
	 */
	public function setInput(\HTML\Input $input);
	
}

?>
