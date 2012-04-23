<?php

namespace Sidus\Properties;

require_once __DIR__.'/property.interface.php';

class String implements propertyInterface{

	protected $value;
	protected $has_changed = false;
	protected $type;

	/**
	 * Set the value for the first time where $value is a correct PHP type
	 * @param $value
	 */
	public function __constructor($value){
		$this->value = (string)$value;
	}

	/**
	 * Return the value for PHP with correct type and no processing for display
	 * @return mixed
	 */
	public function get(){
		return $this->value;
	}

	/**
	 * Set the value of the property where $value is a correct PHP type
	 * Must change $this->has_changed to true;
	 * @param $value
	 * @return boolean
	 */
	public function set($value){
		$value = (string)$value;
		if($this->value != $value){
			$this->value = $value;
			$this->has_changed = true;
		}
	}

	/**
	 * Check if the value has been changed since it's first assignment
	 * @return boolean
	 */
	public function hasChanged(){
		return $this->has_changed;
	}

	/**
	 * Reset the hasChanged property to false
	 */
	public function reset(){
		$this->has_changed = false;
	}

	/**
	 * Return a string ready to be inserted in a SQL query, be sure to escape
	 * special characters properly !
	 * If the database type is not known, there MUST be a fall back with a
	 * standard SQL92 output.
	 * @param \SQL\Database $db handler to database 
	 * @return string
	 */
	public function toDB(\SQL\Database $db){
		return '\''.$db->secureString($this->value).'\'';
	}

	/**
	 * Check if the $value is acceptable for this property
	 * @param $value
	 * @return boolean
	 */
	public function check($value){
		if(is_string($value)){
			return true;
		}
		if(is_object($value)){
			if(method_exists($value, '__toString')){
				return true;
			}
			return false;
		}
		if($value == (string)$value){
			return true;
		}
		return false;
	}

}

?>
