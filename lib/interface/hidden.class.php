<?php

namespace HTML;

class Hidden extends Input {

	public function __construct($name, $default_value = null){
		parent::__construct($name, $default_value);
		$this->type = 'hidden';
	}

}
