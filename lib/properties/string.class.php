<?php

namespace Sidus\Properties;

require_once __DIR__.'/property.interface.php';

class String implements propertyInterface {

	public function __constructor($table_name, $column_name, $pdo_param = \PDO::PARAM_STR, $model_name = null){

	}

	public function __toString(){

	}

	public function canRead($permission_set){
		
	}

	public function canWrite($permission_set){

	}

	public function check($value){

	}

	public function get(){

	}

	public function getColumnName(){

	}

	public function getFullColumnName(){

	}

	public function getInput(){

	}

	public function getModelName(){

	}

	public function getPDOParam(){

	}

	public function getReadAuth(){

	}

	public function getTableName(){

	}

	public function getWriteAuth(){

	}

	public function hasChanged(){

	}

	public function reset(){

	}

	public function set($value){

	}

	public function setInput(\HTML\Input $input){

	}

	public function toDB(){

	}

}

?>
