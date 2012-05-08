<?php

namespace HTML;

class Option extends Element {

	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'disabled', 'label', 'selected', 'value');

	public function __construct($label, $value = null){
		parent::__construct('option');
		$this->value = $value;
		$this->add($label);
	}

}
