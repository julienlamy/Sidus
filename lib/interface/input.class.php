<?php
namespace html;

class Input extends Element{
	
	protected $attributes=array();
	protected $label=null;
	protected $regexp=null;
	protected $prefix='';
	public static protected $authorized_attributes;
	
	public function __construct($name,$type,$default_value=null,$label=null){
		parent::Element('input','');
		$this->attributes['name']=$name;
		$this->attributes['type']=$type;
		if ($default_value !== null){
			$this->attributes['value']=$default_value;
		}
		$this->label=$label;
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
		}else{
			$this->attributes['maxlength']=$value;
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
		}else{
			$this->attributes['size']=$value;
			return $this;
		}
	}
	
	/**
	 * set a regular expression to validate input
	 * @param string $value
	 * @return $this 
	 */
	public function setRegexp($value){
		$this->regexp=$value;
		return $this;
	}
	
	/**
	 * set a prefix for name and id
	 * @param type $value
	 * @return $this 
	 */
	public function setPrefix($value){
		$this->prefix=$value;
		return $this;
	}
	
	/**
	 * Validate the input. Try to match the regexp.
	 * @return boolean
	 * @throws \InvalidArgumentException 
	 */
	public function validate(){
		if($this->regexp !== null){//If you have set a regexp to test input
			$result=preg_replace($this->regexp, '', $this->get_value(false), 1); //replace by nothing
			if($result != ''){//If there is chars left, it means the input doesn't match the regexp...
				throw new \InvalidArgumentException('The input does not match the regexp ('.$this->regexp.')');
			}
		}
		return true;
	}
	
	public function __ToString(){
		$tmp = '';
		if ($this->label !== null){
			$tmp = '<label for="'.$this->prefix.$this->attributes['id'].'">'.$this->label.'</label>';
		}
		$tmp.='<input';
		foreach ($this->attributes as $attribute){
			$tmp.=' '.$attribute[0].'="'.$attribute[1].'"';
		}
		$tmp.='/>';
		return $tmp;
	}
}

Input::$authorized_attributes=array_merge(parent::$authorized_attributes,array('accept','alt','checked','disabled','maxlength','name','readonly','size','src','type','value','onblur','onchange','onclick','ondblclick','onfocus','onmousedown','onmouseover','onmousemove','onmouseout','onmouseup','onkeydown','onkeypress','onkeyup','onselect'));
?>