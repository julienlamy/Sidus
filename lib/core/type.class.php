<?php
namespace Sidus;

class type{
	protected $enabled=false;
	public $name; //name as set in database
	public $class_name; //PHP class name
	public $class_path; //filepath from REAL_PATH
	public $allowed_childs = array(); //array of type->name
	public $allowed_parents = array(); //array of type->name
	public $dependancies = array(); //array of type->name

	public function __construct($name, $class_name='GenericNode', $class_path='lib/nodes/generic.node.class.php'){
		$this->name = (string)$name;
		if(file_exists(REAL_PATH.$class_path)){
			$this->class_path = $class_path;
			require_once REAL_PATH.$class_path;
		}else{
			trigger_error('Unable to find class path : '.$class_path, E_USER_WARNING);
		}
		if(class_exists($class_name)){
			$this->class_name = $class_name;
		} else {
			trigger_error('Class doesn\'t exists : '.$class_name, E_USER_WARNING);
			return;
		}
		$this->enabled=true;
	}
	
	

}

?>
