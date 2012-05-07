<?php

namespace HTML;

class Label extends Element {

	protected $input;
	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'for');

	public function __construct($content, $for = null){
		parent::__construct('label');
		if(is_string($for)){
			$this->for = $for;
		} elseif(is_a($for, '\\HTML\\Input')){
			$this->attributes['for'] = '';
			$this->input = $for;
		}
		$this->attach($content);
	}

	public function addInput(Input $input, $label = null){
		$label = new \HTML\Label($label);
		$this->attach($object);
	}

	public function getFor(){
		if($this->input){
			return $this->input->id;
		}
		return $this->for;
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
