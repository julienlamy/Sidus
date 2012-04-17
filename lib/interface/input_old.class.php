<?php

class Input{

	protected $name; //SQL column name of the field
	protected $label; //Label to be displayed in the HTML
	protected $type; //HTML type (text, password, hidden)
	protected $not_null; //True if the user input can't be null
	protected $default_value; //Default value
	protected $error_msg=null; //If it exists, the message will be displayed when an error occurs.
	protected $regexp=null; //Regular Expression to test the user input
	protected $attributes=''; //HTML attributes for specific purposes
	protected $error=false; //If there is an error
	protected $value=null; //Value passed by user
	protected $validated=false;
	protected $prefix='';

	public function __construct($name, $label=null, $type='text', $default_value=null, $not_null=false){
		$this->name=$name;
		$this->label=$label;
		$this->type=$type;
		$this->not_null=$not_null;
		$this->default_value=$default_value;
	}

	public final function setErrorMessage($error_msg){
		$this->error_msg=$error_msg;
	}

	/**
	 * You can set a regular expression to filter the user-input.
	 */
	public final function set_regexp($regexp){
		$this->regexp=$regexp;
	}

	public final function set_html_attributes($attributes){
		$this->attributes=$attributes;
	}

	public final function get_name(){
		return $this->name;
	}
	
	public final function get_type(){
		return $this->type;
	}

	/**
	 * TODO: We need to do something about displaying user-input like that...
	 */
	public function display(){
		$this->validate();
		$string=$this->display_label();
		$string.='<input name="'.$this->prefix.$this->name.'" id="'.$this->prefix.$this->name.'" type="'.$this->type.'" class="'.$this->name.($this->error?' form_error':'').'" '.$this->attributes; //Minimal html for an input
		if($this->get_value() !== null){//If there was a user input, display it in the field
			$string.=' value="'.$this->value.'"';
		} elseif($this->default_value !== null){//Else, display the default value if it exists
			$string.=' value="'.$this->default_value.'"';
		}
		if($this->regexp !== null){//If there was a regexp, use HTML5 pattern to check input
			$string.=' pattern="'.trim($this->regexp,'/').'"';
		}
		$string.=' />'.$this->display_error();
		return $string;
	}

	/**
	 * return the label tag if there is one
	 */
	protected function display_label(){
		if($this->label != null){//If a label is set, display it
			return '<label for="'.$this->prefix.$this->name.'" class="'.$this->name.'">'.$this->label.'</label>';
		}
		return '';
	}

	/**
	 * Display the error message if there is one
	 */
	protected function display_error(){
		if($this->error && $this->error_msg != null){//Add error message if an error was encountered
			return '<span class="form_error_msg">'.$this->error_msg.'</span>';
		}
		return '';
	}

	/**
	 * Set the value manually.
	 */
	public final function set_value($value){
		$this->value=$value;
	}

	/**
	 * Set the value manually.
	 */
	public final function set_prefix($value){
		$this->prefix=$value;
	}

	/**
	 * Check and return the value or false if the user_input was incorrect.
	 */
	public function get_value($validate=true){
		if($this->value === null){//If no value was set, take the default one
			$this->value=$this->default_value;
			if(isset($_POST[$this->prefix.$this->name])){//If a value is set by the user, take it
				$this->value=$_POST[$this->prefix.$this->name];
			}
		}
		if(!$this->validated && $validate){
			$this->validate();
		}
		if($this->error){//Return false if there is an error
			return false;
		}
		return $this->value;
	}

	/**
	 * Validate the value of the input.
	 */
	public function validate(){
		if($this->validated){
			return!$this->error;
		}
		if($this->regexp !== null){//If you have set a regexp to test input
			$result=preg_replace($this->regexp, '', $this->get_value(false), 1); //replace by nothing
			if($result != ''){//If there is chars left, it means the input doesn't match the regexp...
				$this->error=true;
			}
		}
		if($this->not_null){//If you don't want an empty input
			if($this->get_value(false) == ''){//check if input is empty
				$this->error=true;
			}
		}
		$this->validated=true;
		return!$this->error;
	}

}
