<?php

namespace Sidus\Properties;
use Sidus\Nodes\Permission;

class String implements PropertyInterface {
	
	/**
	 * @see Sidus\Properties\PropertyInterface::$value
	 * @var mixed
	 */
	protected $value;

	/**
	 * @see Sidus\Properties\PropertyInterface::$has_changed
	 * @var boolean
	 */
	protected $has_changed = false;

	/**
	 * @see Sidus\Properties\PropertyInterface::$table_name
	 * @var string
	 */
	protected $table_name;

	/**
	 * @see Sidus\Properties\PropertyInterface::$column_name
	 * @var string
	 */
	protected $column_name;

	/**
	 * @see Sidus\Properties\PropertyInterface::$pdo_param
	 * @var integer
	 */
	protected $pdo_param = \PDO::PARAM_STR;

	/**
	 * @see Sidus\Properties\PropertyInterface::$model_name
	 * @var string
	 */
	protected $model_name;

	/**
	 * @see Sidus\Properties\PropertyInterface::$input
	 * @var class
	 */
	protected $input_class = '\HTML\Input';

	/**
	 * @see Sidus\Properties\PropertyInterface::$input
	 * @var Input
	 */
	protected $input;

	/**
	 * @see Sidus\Properties\PropertyInterface::$node
	 * @var Node
	 */
	protected $node;

	/**
	 * @see Sidus\Properties\PropertyInterface::$read_auth
	 * @var integer
	 */
	protected $read_auth = Permission::READ;

	/**
	 * @see Sidus\Properties\PropertyInterface::$write_auth
	 * @var integer
	 */
	protected $write_auth = Permission::WRITE;

	/**
	 * @see Sidus\Properties\PropertyInterface::__constructor()
	 * @see http://php.net/manual/en/pdo.constants.php Documentation for PDO Params
	 * @param string $table_name Table name of the property in the Database
	 * @param string $column_name Column name of the property in the Database
	 * @param integer $pdo_type The PDO param type of the value in the DB
	 * @param string $model_name If different from column name, the PHP name
	 */
	public function __constructor(\Sidus\Nodes\Node $node, $table_name, $column_name, $pdo_param = \PDO::PARAM_STR, $model_name = null){
		$this->node = $node;
		$this->table_name = (string)$table_name;
		$this->column_name = (string)$column_name;
		$this->pdo_param = $pdo_param;
		$this->model_name = (string)($model_name ? $model_name : $column_name);
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::get()
	 * @return mixed $value
	 */
	public function get(){
		return (string)$this->value;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::set()
	 * @param mixed $value
	 * @return boolean
	 */
	public function set($value){
		try{
			$this->value = (string)$value;
		} catch(\Exception $e){
			return false;
		}
		$this->has_changed = true;
		return true;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::hasChanged()
	 * @return boolean
	 */
	public function hasChanged(){
		return $this->has_changed;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::reset()
	 * @return null
	 */
	public function reset(){
		$this->has_changed = false;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::toDB()
	 * @return mixed $value
	 */
	public function toDB(){
		return $this->value;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::check()
	 * @param $value
	 * @return boolean
	 */
	public function check($value){
		try{
			$tmp = (string)$value;
		} catch(\Exception $e){
			return false;
		}
		return true;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::__toString()
	 * @return string $value
	 */
	public function __toString(){
		return (string)$this->value;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getTableName()
	 * @return string $table_name
	 */
	public function getTableName(){
		return $this->table_name;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getColumnName()
	 * @return string $column_name
	 */
	public function getColumnName(){
		return $this->column_name;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getFullColumnName()
	 * @return string ${$table_name.'.'.$column_name}
	 */
	public function getFullColumnName(){
		return $this->table_name.'.'.$this->column_name;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getModelName()
	 * @return string $mode_name
	 */
	public function getModelName(){
		return $this->model_name;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getPDOParam()
	 * @return string $value
	 */
	public function getPDOParam(){
		return $this->pdo_param;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::getInput()
	 * @return string $value
	 */
	public function getInput(){
		if(!$this->input){
			$this->input = new \HTML\Input("n[{$this->node->id}][{$this->getModelName()}]", $this->get());
		}
		return $this->input;
	}

	/**
	 * @see Sidus\Properties\PropertyInterface::setInput()
	 * @param \HTML\Input $input
	 */
	public function setInput(\HTML\Input $input);

	/**
	 * @see Sidus\Properties\PropertyInterface::getReadAuth()
	 * @return integer $read_auth
	 */
	public function getReadAuth();

	/**
	 * @see Sidus\Properties\PropertyInterface::getWriteAuth()
	 * @return integer $write_auth
	 */
	public function getWriteAuth();

	/**
	 * @see Sidus\Properties\PropertyInterface::canRead()
	 * @param integer $permission_set
	 * @return boolean
	 */
	public function canRead($permission_set);

	/**
	 * @see Sidus\Properties\PropertyInterface::canWrite()
	 * @param integer $permission_set
	 * @return boolean
	 */
	public function canWrite($permission_set);

}

?>
