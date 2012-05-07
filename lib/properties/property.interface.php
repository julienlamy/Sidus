<?php

namespace Sidus\Properties;

use Sidus\Nodes\Permission;

/**
 * Common interface for all properties, define an set of rules to exchange vars
 * from Database to the end user interface.
 * All methods must check the permission set of the user before doing anything.
 */
interface PropertyInterface {

	/**
	 * The actual value in the PHP system
	 * @var mixed
	 */
	protected $value;

	/**
	 * Define if the value has changed from it's original value
	 * @var boolean
	 */
	protected $has_changed = false;

	/**
	 * Table name in Database
	 * @var string
	 */
	protected $table_name;

	/**
	 * Column name in Database
	 * @var string
	 */
	protected $column_name;

	/**
	 * PDO Parameter type used for prepared statements
	 * @var integer
	 */
	protected $pdo_param = \PDO::PARAM_STR;

	/**
	 * Model name of the property (column name by default)
	 * @var string
	 */
	protected $model_name;

	/**
	 * Input object that implements the \HTML\InputInterface
	 * @var Input
	 */
	protected $input;

	/**
	 * Node associated to this property
	 * @var Node
	 */
	protected $node;

	/**
	 * Needed permission to read the value
	 * @var integer
	 */
	protected $read_auth = Permission::READ;

	/**
	 * Needed permission to write the value
	 * @var integer
	 */
	protected $write_auth = Permission::WRITE;

	/**
	 * Set the value for the first time where $value is a correct PHP type
	 * @see http://php.net/manual/en/pdo.constants.php Documentation for PDO Params
	 * @param string $table_name Table name of the property in the Database
	 * @param string $column_name Column name of the property in the Database
	 * @param integer $pdo_type The PDO param type of the value in the DB
	 * @param string $model_name If different from column name, the PHP name
	 */
	public function __constructor(\Sidus\Nodes\Node $node, $table_name, $column_name, $pdo_param = \PDO::PARAM_STR, $model_name = null);

	/**
	 * Return the value for PHP with correct type and no processing for display
	 * @return mixed $value
	 */
	public function get();

	/**
	 * Set the value of the property where $value is a correct PHP type
	 * Call $this->check($value) to ensure correct input and can throw an error
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
	 * Returns the full column name (with table name) of the property
	 * @return string ${$table_name.'.'.$column_name}
	 */
	public function getFullColumnName();

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

	/**
	 * Get needed permission set to read the value
	 * @return integer $read_auth
	 */
	public function getReadAuth();

	/**
	 * Get needed permission set to write to value
	 * @return integer $write_auth
	 */
	public function getWriteAuth();

	/**
	 * Check if permission set can read the value
	 * @param integer $permission_set
	 * @return boolean
	 */
	public function canRead($permission_set);

	/**
	 * Check if permission set can write to value
	 * @param integer $permission_set
	 * @return boolean
	 */
	public function canWrite($permission_set);

}

?>
