<?php
require_once 'class.ui_input.php';

class ui_email extends ui_input{

	protected $type='text'; //HTML type (text, password, hidden)
	protected $regexp='/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/'; //Regular Expression to test the user input

	public function __construct($name, $label=null, $default_value=null, $not_null=false){
		$this->name=$name;
		$this->label=$label;
		$this->not_null=$not_null;
		$this->default_value=$default_value;
	}

}
