<?php

namespace HTML;

use \Symfony\Component\HttpFoundation\ApacheRequest;

class Select extends Input{

	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'disabled', 'name', 'multiple', 'size', 'onblur', 'onchange', 'onfocus', 'onselect');
	protected $options = array();
	protected $selected_index;

	public function __construct($name, array $options, $selected_index = null){
		Element::__construct('select');
		$this->name = $name;
		$this->tag_name = 'select';
		$this->options = $options;
		if($selected_index){
			if(!array_key_exists($selected_index, $this->options)){//check if input is valid
				trigger_error('ABoard : Wrong default value: '.$selected_index.' for select', E_USER_WARNING);
			}
		}
		$this->selected_index = $selected_index;
		$this->hydrateValueFromRequest();
	}

	public function hydrateValueFromRequest($from = null){
		$request = ApacheRequest::createFromGlobals()->request;
		$this->selected_index = $request->get($this->name, $this->value, true);
	}
	
	public function __toString(){
		foreach($this->options as $key => $val){
			$option = new Option($val, $key);
			if($key == $this->selected_index){
				$option->selected = 'selected';
			}
			$this->add($option);
		}
		return parent::__toString();
	}

	public function setSelectedIndex($value){
		$this->selected_index = htmlentities($value);
	}

	/**
	 * Validate the input.
	 * @return boolean
	 */
	public function validate(){
		if(isset($this->selected_index) && !array_key_exists($this->selected_index, $this->options)){//check if input is valid
			$this->addClass('form_error');
			return false;
		}
		return true;
	}

}