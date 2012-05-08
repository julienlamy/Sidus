<?php

namespace HTML;

class Email extends Input{

	public function __construct($name, $default_value=null){
		parent::__construct($name, $default_value);
		$this->pattern='/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/';
		$this->title='Please enter a valid email address.';
	}

}
