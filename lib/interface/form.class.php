<?php

namespace HTML;

class Form extends Element {

	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'action', 'accept', 'accept-charset', 'enctype', 'method', 'name', 'target');

	public function __construct($action = '', $method = 'post'){
		parent::__construct('form');
		$this->action = $action;
		$this->method = $method;
	}

	public function addInput(Input $input, $label = null){
		$label = new \HTML\Label($label, $input);
		$this->attach($label);
		$this->attach($input);
		return $this;
	}

	public function getInputsOnly(){
		$tmp = new Form($this->action, $this->method);
		foreach($this as $element){
			if(is_a($element, '\\HTML\\Input')){
				$tmp->attach($element);
			}
		}
		return $tmp;
	}


	public function validate(){
		foreach($this as $element){
			if(method_exists($element, 'validate') && !$element->validate()){
				return false;
			}
		}
		return true;
	}

}
