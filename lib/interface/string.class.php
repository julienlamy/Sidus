<?php

namespace HTML;

class String {

	protected $value = '';

	public function __construct($value){
		$this->value = htmlentities($value);
	}

	public function __toString(){
		return $this->value;
	}

}
