<?php
namespace HTML;

class Menu{
	protected $board;
	protected $elements=array();
	protected $attributes=array();
	protected $id;
	protected $class;
	protected $allowed_types=array('ui_separator','ui_button');
	
	function __construct(proto_board $board,$id='menu',$class=null){
		$this->board=$board;
		$this->id=$id;
		$this->class=$id;
		if($class!=null){
			$this->class=$class;
		}
	}
	
	/**
	 * 
	 */
	public function add(object $el,$index=null){
		if(!is_integer($index)){
			//TODO : Throw some kind of error
			return false;
		}
		if(!in_array($this->allowed_typed,get_class($el))){
		  //TODO : Throw some kind of error
			return false;
		}
		if($index==null){
			$this->elements[]=el;
			return true;
		}
		$tmp=$this->elements;//Save elements
		$this->elements=array();//Reset array
		$done=false;//Indicate that the element hasn't been inserted yet
		foreach($tmp as $key=>$value){
			if($key==$index){//If the index is the one proposed
				$this->elements[$key]=$el;//Insert the element
				$key++;
				$done=true;
			}
			$this->elements[$key]=$value;//In any case, insert the next element
			$key++;
		}
		if(!$done){//If element hasn't been inserted (meaning index is higher than the number of elements)
      $this->elements[$index]=$el;//Insert with index.
		}
		return true;
	}
	
	public function get($index){
		if(isset($this->elements[$index])){
			return $this->elements[$index];
		}
		return false;
	}
	
	/**
	 * WARNING: This function only set a entry in the menu informations
	 */
	public function set_attribute($key,$value){
	  if(!is_string($key) || !is_string($value)){
			//TODO : Throw some kind of error
	    return false;
	  }
		$this->attributes[$key]=$value;
		return true;
	}
	
	/**
	 * See PHP doc for current()
	 */
	public function current(){
		return current($this->elements);
	}
	
	/**
	 * See PHP doc for reset()
	 */
	public function reset(){
		return reset($this->elements);
	}
	
	/**
	 * See PHP doc for prev()
	 */
	public function prev(){
		return prev($this->elements);
	}
	
	/**
	 * See PHP doc for next()
	 */
	public function next(){
	  return next($this->elements);
	}
	
	/**
	 * See PHP doc for end()
	 */
	public function end(){
	  return end($this->elements);
	}

	/**
	 * See PHP doc for count()
	 */
	public function count(){
		return count($this->elements);
	}
	
	/**
	 * TODO
	 */
	public function remove($el){
	  $key=$this->indexof($el);
		if($key===false){
			return false;
		}
		return $this->delete($key);
	}
	
	public function delete($key){
	  if(!isset($this->elements[$key])){
	    //TODO : ERROR
	    return false;
		}
		unset($this->elements[$key]);
		return true;
	}
	
	public function indexof($el){
	  return array_search($el,$this->elements,true);
	}
	
	/**
	 * TODO
	 */
	public function sort($key){

	}
	
	public function exists($el){
		if($this->indexof($el)===false){
			return false;
		}
		return true;
	}
	
	public function get_html(){
	  $html='<ul id="'.$this->id.'" class="'.$this->class.'" ';
	  foreach($this->attributes as $key=>$value){
			$html.=$key.'="'.$value.'" ';
		}
		$html.='/>';
	  foreach($this->elements as $value){
			$html.='<li>'.$value->get_html().'</li>';
		}
	  $html.='</ul>';
		return $html;
	}


}

?>
