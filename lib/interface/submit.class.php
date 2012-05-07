<?php

namespace HTML;

class Submit extends Input {

	public function __construct($name, $default_value = null){
		parent::__construct($name, $default_value);
		$this->type = 'submit';
	}

}
