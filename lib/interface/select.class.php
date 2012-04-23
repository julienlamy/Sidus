<?php
namespace HTML;

require_once 'class.Input.php';

class Select extends Input{

	protected $options=array();

	public function __construct($name, $label, array $options, $default_value=null){
		parent::__construct($name, $label);
		$this->options=$options;
		if($default_value !== null){
			if(!array_key_exists($default_value, $this->options)){//check if input is valid
				trigger_error('ABoard : Wrong default value: '.$default_value.' for select', E_USER_WARNING);
				exit;
			}
		}
		$this->default_value=$default_value;
	}

	/**
	 * We need to do something about displaying user-input like that...
	 */
	public function display(){
		//$this->validate();
		$string=$this->display_label();
		$string.='<select name="'.$this->prefix.$this->name.'" id="'.$this->prefix.$this->name.'" class="'.$this->name.'" '.$this->attributes;
		if($this->get_value() !== null){//If there was a user input, display it in the field
			$default=$this->value;
			if($this->error){//But there may be an error with that input
				$string.=' class="form_error"';
			}
		} elseif($this->default_value !== null){//Else, display the default value if it exists
			$default=$this->default_value;
		} else {
			$default=null;
		}
		$string.='>';
		foreach($this->options as $key=>$val){
			$string.='<option value="'.$key.'"';
			if($key == $default){
				$string.=' selected="selected"';
			}
			$string.='>'.$val.'</option>';
		}
		$string.='</select>'.$this->display_error();
		return $string;
	}

	public function validate(){
		if($this->validated){
			return !$this->error;
		}
		if(!array_key_exists($this->get_value(false), $this->options)){//check if input is valid
			$this->error=true;
		}
		$this->validated=true;
		return !$this->error;
	}

}