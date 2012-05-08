<?php

namespace HTML;

class Textarea extends Input {

	public function __construct($name, $default_value = null){
		Element::__construct('textarea', $default_value);
		$this->name = $name;
	}

}
