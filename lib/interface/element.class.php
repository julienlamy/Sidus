<?php

namespace HTML;

class Element extends \Collection{

	protected $tag_name;
	protected $attributes = array();
	protected $content = '';
	protected $is_void_element = false;
	protected static $void_elements = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
	protected static $forbidden_elements = array('base', 'head', 'html', 'meta', 'param', 'script', 'style');
	
	/**
	 * Common html attributes.
	 * WARNING ! The following elements don't support all the event
	 * attributes : base, bdo, br, frame, frameset, head, html, iframe, meta,
	 * param, script, style, and title
	 * @see http://www.w3schools.com/tags/ref_eventattributes.asp
	 * @var Array $authorized_attributes
	 */
	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml_lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup');

	/**
	 *
	 * @param string $tag_name
	 * @param string $content
	 * @throws \InvalidArgumentException 
	 */
	public function __construct($tag_name, $content = ''){
		if(in_array(strtolower($tag_name), $this::$forbidden_elements)){
			throw new \InvalidArgumentException('forbidden element tag name ('.$tag_name.')');
		}
		$this->tag_name = $tag_name;
		if(array_search($this->tag_name, $this::$void_elements)){
			$this->is_void_element = true;
		} else {
			$this->content = $content;
		}
	}

	public function __get($key){
		$getter_name = 'get'.\Utils::Camelize($key);
		if(method_exists($this, $getter_name)){
			return $this->$getter_name();
		} elseif(array_key_exists($this->attributes, $key)){
			return $this->attributes[$key];
		}
		return null;
	}

	public function getContent(){
		return $this->content;
	}

	public function __set($key, $value){
		$setter_name = 'set'.\Utils::Camelize($key);
		if(method_exists($this, $setter_name)){
			return $this->$setter_name($value);
		} 
		if(!in_array(strtolower($key), $this::$authorized_attributes)){
			throw new \InvalidArgumentException("Forbidden attribute $key for element {$this->tag_name}");
		}
		$this->attributes[$key] = htmlentities($value);
		return $this;
	}

	public function setContent($value){
		if(array_search($this->tag_name, $this::$void_elements)){
			//throw new Exception();//@TODO
			return $this;
		}
		$this->content = $value;
		return $this;
	}

	/**
	 * Set a new class. Delete all previously added classes
	 * @param string $value
	 * @return Element current element
	 */
	public function setClass($value){
		$this->attributes['class'] = $value;
		return $this;
	}

	/**
	 * Add a new class. Doesn't remove previously added classes.
	 * @param string $value
	 * @return Element current element 
	 */
	public function addClass($value){
		$this->attributes['class'] .= ' '.$value;
		return $this;
	}

	/**
	 * Return the element tag.
	 * @return string 
	 */
	public function __toString(){
		$tmp = '<'.$this->tag_name;
		foreach($this->attributes as $key => $attribute){
			$tmp .= ' '.$key.'="'.$attribute.'"';
		}
		if($this->is_void_element){
			$tmp .= '/>';
		} else {
			$tmp .= '>'.$this->content.'</'.$this->tag_name.'>';
		}
		return $tmp;
	}

}

?>
