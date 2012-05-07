<?php

namespace HTML;

use \Symfony\Component\HttpFoundation\ApacheRequest;

class Input extends Element {

	protected $regexp = null;
	protected $prefix = '';
	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'accept', 'alt', 'checked', 'disabled', 'maxlength', 'name', 'readonly', 'size', 'src', 'type', 'value', 'onblur', 'onchange', 'onfocus', 'onselect');

	public function __construct($name, $default_value = null){
		parent::__construct('input');
		$this->name = $name;
		$this->setIdFromName($name);
		$this->type = 'text';
		if($default_value){
			$this->value = $default_value;
		}
		$this->hydrateValueFromRequest();
	}

	public function hydrateValueFromRequest($from = null){
		$request = ApacheRequest::createFromGlobals();
		$this->value = $request->request->get($this->name, $this->value, true);
	}

	/**
	 * Set maxlength attribute
	 * @param integer $value
	 * @throws \UnexpectedValueException
	 * @return $this
	 */
	public function setMaxlenght($value){
		if(!is_integer($value)){
			throw new \UnexpectedValueException('Unexcepted value for maxlength attribute ('.$value.')');
		} else {
			$this->attributes['maxlength'] = $value;
			return $this;
		}
	}

	/**
	 * set size attribute
	 * @param integer $value
	 * @throws \UnexpectedValueException
	 * @return $this 
	 */
	public function setSize($value){
		if(!is_integer($value)){
			throw new \UnexpectedValueException('Unexcepted value for size attribute ('.$value.')');
		} else {
			$this->attributes['size'] = $value;
			return $this;
		}
	}

	/**
	 * set a regular expression to validate input
	 * @param string $value
	 * @return $this 
	 */
	public function setRegexp($value){
		$this->regexp = $value;
		return $this;
	}

	/**
	 * set a prefix for name and id
	 * @param type $value
	 * @return $this 
	 */
	public function setPrefix($value){
		$this->prefix = $value;
		return $this;
	}

	public function setValue($value){
		$this->attributes['value'] = htmlentities($value);
	}

	/**
	 * Validate the input. Try to match the regexp.
	 * @return boolean
	 * @throws \InvalidArgumentException 
	 */
	public function validate(){
		if($this->regexp){//If you have set a regexp to test input
			$result = preg_replace($this->regexp, '', $this->value, 1); //replace by nothing
			if($result != ''){//If there is chars left, it means the input doesn't match the regexp...
				$this->addClass('form_error');
				return false;
			}
		}
		return true;
	}

	public function setIdFromName($name){
		$this->id = \Utils::slugify($name, '_');
	}

}
