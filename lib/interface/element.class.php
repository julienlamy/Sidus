<?php

namespace HTML;

class Element extends \Collection {

	protected $tag_name;
	protected $attributes = array();
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
	protected static $authorized_attributes = array('id', 'class', 'style', 'title', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup');

	/**
	 *
	 * @param string $tag_name
	 * @param mixed $content
	 * @throws \InvalidArgumentException 
	 */
	public function __construct($tag_name, $content = null){
		if(in_array(strtolower($tag_name), $this::$forbidden_elements)){
			throw new \InvalidArgumentException('forbidden element tag name ('.$tag_name.')');
		}
		$this->tag_name = $tag_name;
		if(array_search($this->tag_name, $this::$void_elements)){
			$this->is_void_element = true;
		} elseif($content){
			$this->add($content);
		}
	}

	public function __get($key){
		$getter_name = 'get'.\Utils::Camelize($key);
		if(method_exists($this, $getter_name)){
			return $this->$getter_name();
		}
		if(array_key_exists($key, $this->attributes)){
			return $this->attributes[$key];
		}
		return null;
	}

	public function getAll(){
		$tmp = '';
		foreach($this as $element){
			$tmp .= $element;
		}
		return $tmp;
	}

	public function __set($key, $value){
		$setter_name = 'set'.\Utils::Camelize($key);
		if(method_exists($this, $setter_name)){
			$this->$setter_name($value);
		}
		if(!in_array(strtolower($key), $this::$authorized_attributes)){
			throw new \InvalidArgumentException("Forbidden attribute $key for element {$this->tag_name}");
		}
		$this->attributes[$key] = htmlentities($value);
	}

	public function clear(){
		$this->removeAll($this);
		return $this;
	}

	public function attach($object, $data = null){
		if(array_search($this->tag_name, $this::$void_elements)){
			throw new \UnexpectedValueException("Can't add content to {$this->tag_name}: void element");
		}
		if(is_array($object)){
			foreach($object as $tmp){
				$this->attach($tmp);
			}
		} elseif(is_object($object)){
			parent::attach($object, $data);
		} else {
			try {
				$object = new String($object);
			} catch(Exception $e){
				throw new \UnexpectedValueException("Invalid object type, must be either an object, an array or a castable to a string");
			}
			parent::attach($object, $data);
		}
		return $this;
	}

	public function add($object, $data = null){
		return $this->attach($object, $data);
	}

	/**
	 * Set a new class. Delete all previously added classes
	 * @param string $value
	 * @return Element current element
	 */
	public function setClass($value){
		$this->attributes['class'] = htmlentities($value);
		return $this;
	}

	/**
	 * Add a new class. Doesn't remove previously added classes.
	 * @param string $value
	 * @return Element current element 
	 */
	public function addClass($value){
		if(!isset($this->attributes['class'])){
			$this->attributes['class'] = htmlentities($value);
		} else {
			$this->attributes['class'] .= ' '.htmlentities($value);
		}
		return $this;
	}

	/**
	 * Return the element tag.
	 * @return string 
	 */
	public function __toString(){
		$tmp = '<'.$this->tag_name;
		foreach($this->attributes as $key => $attribute){
			$tmp .= ' '.$key.'="'.$this->$key.'"';
		}
		if($this->is_void_element){
			$tmp .= '/>';
		} else {
			$tmp .= '>'.$this->getAll().'</'.$this->tag_name.'>';
		}
		return $tmp;
	}

}

?>
