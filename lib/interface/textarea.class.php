<?php
namespace HTML;

class Textarea extends Input{

	public function __construct($name, $label=null, $not_null=false, $default_value=null){
		$this->name=$name;
		$this->label=$label;
		$this->not_null=$not_null;
		$this->default_value=$default_value;
	}

	/**
	 * We need to do something about displaying user-input like that...
	 */
	public function __toString(){
		$string='';
		$string.=$this->display_label();

		$string.='<textarea name="'.$this->prefix.$this->name.'" id="'.$this->prefix.$this->name.'" class="'.$this->name.'" '.$this->attributes;
		if($this->error){//But there may be an error with that input
			$string.=' class="form_error"';
		}
		$string.='>'; //Minimal html for an input

		if($this->value !== null){//If there was a user input, display it in the field
			$string.=$this->value;
		} elseif($this->default_value !== null){//Else, display the default value if it exists
			$string.=$this->default_value;
		}

		$string.='</textarea>';
		$string.=$this->display_error();

		return $string;
	}

}
