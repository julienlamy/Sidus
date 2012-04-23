<?php
namespace HTML;

class Checkbox extends Input{
	protected $array=false;
	protected $displayed_name;
	protected $subname='1';

	public function __construct($name, $label=null, $default_value=null){
		$this->displayed_name=$name;
		$this->name=$name;
		$offset=strpos($this->name,'.');
		if($offset!=false){//Don't care about first position (false==0)
			$this->subname=substr($this->name, $offset+1);
			$this->displayed_name=substr($this->name,0,$offset);
			$this->array=true;
		}
		$this->label=$label;
		$this->default_value=(int)(bool)$default_value;
	}

	/**
	 * TODO: We need to do something about displaying user-input like that...
	 */
	public function display(){
		$this->validate();
		$string=$this->display_label();
		$string.='<input name="active_'.$this->prefix.$this->displayed_name.($this->array?'_'.$this->subname:'').'" type="hidden" value="1" />';
		$string.='<input name="'.$this->prefix.$this->displayed_name.($this->array?'[]':'').'" id="'.$this->prefix.$this->displayed_name.($this->array?'_'.$this->subname:'').'" type="checkbox" class="'.$this->displayed_name.'" value="'.($this->array?$this->subname:'1').'" '.$this->attributes; //Optimal html for an input
		if($this->get_value()===1){//If there was a user input, display it in the field
			$string.=' checked="checked"';
		} elseif($this->default_value===1){//Else, display the default value if it exists
			$string.=' checked="checked"';
		}
		$string.=' />'.$this->display_error();
		return $string;
	}

	/**
	 * Check and return the value or false if the user_input was incorrect.
	 */
	public function get_value($validate=true){
		if($this->value === null){//If no value was set, take the default one
			$this->value=$this->default_value;
			if($this->array){
				if(isset($_POST['active_'.$this->prefix.$this->displayed_name.'_'.$this->subname])){//If the field is present in the form
					if(isset($_POST[$this->prefix.$this->displayed_name])){//This means the input exists
						if(in_array($this->subname,$_POST[$this->prefix.$this->displayed_name])){//If the field is checked in the form
							$this->value=1;
						} else {
							$this->value=0;
						}
					}
				}
			} else {
				if(isset($_POST['active_'.$this->prefix.$this->displayed_name])){//If the field is present in the form
					if(isset($_POST[$this->prefix.$this->displayed_name])){//If the field is checked in the form
						$this->value=1;
					} else {
						$this->value=0;
					}
				}
			}
		}
		$this->error=false;
		return $this->value;
	}

	/**
	 * Validate the value of the input.
	 */
	public function validate(){
		return true;
	}

}
