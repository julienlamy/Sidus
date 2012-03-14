<?php

namespace Sidus;

class Collection implements IteratorAggregate, Traversable, ArrayAccess, Serializable, Countable{

	protected $elements = array();

	public function __construct($elements = null){
		if($elements !== null){
			if(is_array($elements)){
				$this->elements = $elements;
			} elseif(is_a($elements, __CLASS__)){
				$this->elements = $elements->toArray();
			} else {
				$this->add($elements);
			}
		}
	}

	public function __toString(){
		return $this->implode(', ');
	}

	protected function sanitizeIndex($index){
		if(!is_int($index)){
			error_log('$index should be an integer, inserting object at the end of the collection', E_USER_NOTICE);
			return null;
		}
		return $index;
	}

	public function add($o, $index = null){
		$index = $this->sanitizeIndex($index);
		if($index === null){
			$this->elements[] = $o;
			return true;
		}
		$this->elements = array_splice($this->elements, $index, 0, $o);
		return true;
	}

	public function addAll($mixed, $index = null){
		$index = sanitizeIndex($index);
		$i = 0;
		if(method_exists($mixed, 'toArray')){
			$mixed = $mixed->toArray();
		}
		if(!is_array($mixed)){
			$this->add($mixed, $index);
		}
		foreach($mixed as $el){
			$this->add($el, $index !== null ? $index + $i : null);
			$i++;
		}
	}

	public function addLast($o){
		$this->elements[] = $o;
		return true;
	}

	public function addFirst($o){
		$this->add($o, 0);
		return true;
	}

	public function clear(){
		$this->elements = array();
	}

	public function contains($o){
		if($this->indexOf($o) === false){
			return false;
		}
		return true;
	}

	public function containsAll(Collection $c){
		foreach($c->toArray() as $o){
			if(!$this->contains($o)){
				return false;
			}
		}
		return true;
	}

	public function equals(Collection $c){
		if($this === $c){
			return true;
		}
		if($this->toArray() === $c->toArray()){
			return true;
		}
		return false;
	}

	public function isEmpty(){
		return empty($this->elements);
	}

	public function remove($o){
		$key = $this->indexOf($o);
		if($key === false){
			return false;
		}
		return $this->delete($key);
	}

	public function removeAll(Collection $c){
		foreach($c->toArray() as $o){
			$this->remove($o);
		}
	}

	public function retainAll(Collection $c){
		
	}

	public function count(){
		return count($this->elements);
	}

	public function size(){
		return $this->count();
	}

	public function toArray(){
		return $this->elements;
	}

	public function get($index){
		if(isset($this->elements[$index])){
			return $this->elements[$index];
		}
		return null;
	}
	
	public function getAll(){
		return $this->elements;
	}

	public function getFirst(){
		return $this->elements[0];
	}

	public function getLast(){
		return $this->elements[$this->count() - 1];
	}

	public function set($index, $o){
		$this->elements[$index] = $o;
	}
	
	public function setAll($elements){
		$elements = new Collection($elements);
		$this->elements=array_merge($this->elements, $elements->toArray());
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

	public function delete($key){
		if(!isset($this->elements[$key])){
			return false;
		}
		unset($this->elements[$key]);
		return true;
	}

	public function indexOf($o){
		return array_search($o, $this->elements, true);
	}

	public function lastIndexOf($o){
		//TODO
	}

	public function sort($key){
		//TODO
	}

	public function subCollection($from, $to){
		//TODO
	}

	public function removeRange($from, $to){
		//TODO
	}

	public function removeFirst(){
		array_splice($this->elements, 0, 1);
	}

	public function removeLast(){
		array_pop($this->elements);
	}

	public function __invoke($index=null){
		if($index === null){
			return $this->elements;
		}
		return $this->get($index);
	}

	public function implode($glue){
		return implode($glue, $this->toArray());
	}
	
	public function getKeys(){
		$keys=array();
		foreach($this->elements as $key=>$el){
			$keys[]=$key;
		}
		return $keys;
	}

}

?>
