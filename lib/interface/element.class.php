<?php
namespace html;

class Element extends Collection{
	
	protected $element_type;
	protected $attribute=array();
	protected $content;
	
	static protected $unauthorized_element=array('base','head','html','meta','param','script','style');
	static protected $authorized_attributes=array('id','class','style','title','dir','lang','xml_lang','accesskey','tabindex');
	
	public function __construct($element_type,$content=''){
		if(in_array(strtolower($element_type),$this::$unauthorized_element)){
			throw new \InvalidArgumentException('Unauthorized element type in Element.class ('.$element_type.')');
		}else{
			$this->element_type = $element_type;
			$this->content=$content;
		}
	}
	
	public function __get($key){
		if(method_exists($this, 'get'.Utils::Camelize($key))){
			return $this->call_user_func('get'.Utils::Camelize($key));
		}elseif(array_key_exists($this->attributes, $key)){
			return $this->attributes[$key];
		}else{
			return false;
		}
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function __set($key, $value){
		if(method_exists($this, 'set'.Utils::Camelize($key))){
			return $this->call_user_func('set'.Utils::Camelize($key));
		}else{
			if(!in_array(strtolower($key),$this::$authorized_attributes)){
				throw new \InvalidArgumentException('Unauthorized attribute for element in Element.class ('.$key.')');
			}else{
				$this->attributes[$key]=$value;
				return $this;
			}
		}
	}
	
	public function setContent($value){
		$this->content=$value;
		return $this;
	}
	
	/**
	 * Set a new class. Delete all class previously added
	 * @param string $value
	 * @return Element current element
	 */
	public function setClass($value){
		$this->attributes['class']=$value;
		return $this;
	}
	
	/**
	 * Add a new class. Do not remove previously added classes.
	 * @param string $value
	 * @return Element current element 
	 */
	public function addClass($value){
		$this->attributes['class'].=' '.$value;
		return $this;
	}
	
	/**
	 * Return the element tag.
	 * @return string 
	 */
	public function __toString(){
		$tmp='<'.$this->element_type;
		if(count($this->attributes)!=0){
			foreach ($this->attributes as $attribute){
				$tmp.=' '.$attribute[0].'="'.$attribute[1].'"';
			}
		}
		$tmp.='>';
		if($this->content != ''){
			$tmp.=$this->content.'</'.$this->element_type.'>';;
		}else{
			$tmp.='/>';
		}
		return $tmp;
		
	}
}

?>
