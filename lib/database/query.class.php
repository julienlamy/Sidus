<?php

namespace SQL;

require_once __DIR__.'/db.interface.php';
require_once __DIR__.'/../core/collection.class.php';

use Sidus\Collection;

class Query{
//	protected $db;
	protected $stmt;
	protected $or_stmt;
	protected $table;
	protected $tables;
	protected $columns;
	protected $values;
	protected $expr;
	protected $order;
	protected $limit;
	protected $offset;

	const DELETE='DELETE';
	const INSERT='INSERT';
	const REPLACE='REPLACE';
	const SELECT='SELECT';
	const UPDATE='UPDATE';

	public function __construct($stmt){
		//$this->db = Core()->db();
		$this->stmt = $stmt;
		$this->tables = new Collection();
		$this->columns = new Collection();
		$this->values = new Collection();
		$this->order = new Collection();
	}

	public function __set($name, $value){
		switch($name){
			case 'stmt':
				$this->stmt = $value;
			case 'or_stmt':
				$this->or_stmt = $value; //TODO check authorized ()
			case 'table':
				$this->table = $value;
			case 'tables':
				$this->tables = new Collection($value);
			case 'columns':
				$this->columns = new Collection($value);
			case 'values':
				$this->values = new Collection($value);
			case 'expr':
				$this->expr = $value; //TODO
			case 'order':
				$this->order = new Collection($value);
			case 'limit':
				$this->limit = $value;
			case 'offset':
				$this->offset = $value;
		}
	}

	public function __get($name){
		if(isset($this->$name)){
			return $this->$name;
		}
	}

	public function __toString(){
		switch($this->stmt){
			case self::DELETE:
				$query = $this->stmt.' FROM ';
				$query.=$this->table;
				if($this->expr){
					$query.=' WHERE '.$this->expr;
				}
				if(!$this->order->isEmpty()){
					$query.=' ORDER BY '.$this->order;
				}
				if($this->limit){
					$query.=' LIMIT '.$this->limit;
				}
				if($this->offset){
					$query.=' OFFSET '.$this->offset;
				}
				return $query.';';
			case self::INSERT:
			case self::REPLACE:
				$query = $this->stmt;
				if($this->or_stmt){
					$query.=' '.$this->or_stmt;
				}
				$query.=' INTO ';
				$query.=$this->table;
				if($this->values->count()){
					$query.=' ('.implode(',', $this->values->getKeys()).')';
					$query.=' VALUES ('.$this->sanitize($this->values)->implode(',').')';
				}else{
					$query.=' DEFAULT VALUES';
				}
				return $query.';';
			case self::SELECT:
				return $query;
			case self::UPDATE:
				return $query;
			default:
			//TODO Generic expression ?
		}
		return $this->stmt;
	}

	public function orRollback(){
		$this->or_stmt = 'OR ROLLBACK';
	}

	public function orAbort(){
		$this->or_stmt = 'OR ABORT';
	}

	public function orReplace(){
		$this->or_stmt = 'OR REPLACE';
	}

	public function orFail(){
		$this->or_stmt = 'OR FAIL';
	}

	public function orIgnore(){
		$this->or_stmt = 'OR IGNORE';
	}

	public function sanitize($elements){
		return $elements;
		$elements = new Collection($elements);
		$safe_elements=array();
		foreach($elements->toArray() as $key=>$value){
			if(is_numeric($value)){
				$safe_elements[$key]=$value;
			} elseif(is_string($value)){
				$safe_elements[$key]=$this->secureString($value);
			} elseif(is_object($value)){
				if(method_exists($value, 'toDB')){
					$safe_elements[$key]=$value->toDB();
				} elseif(method_exists($value, '__toString')){
					$safe_elements[$key]=$this->secureString((string)$value);
				}
			} else {//Last try...
				$safe_elements[$key]=$this->secureString((string)$value);
			}
		}
		return $safe_elements;
	}

	public function exec(){ //Execute a query
		return $this->db->exec($this);
	}

	public function getSingle(){ //Return the first value of the query
		return $this->db->getSingle($this);
	}

	public function getRow(){ //Return a row
		return $this->db->getRow($this);
	}

	public function getArray(){ //Return an array for multiple rows
		return $this->db->getArray($this);
	}

	public function getLastId(){ //Get the last id inserted
		return $this->db->getLastId();
	}

	public function getLastError(){ //Get the last error
		return $this->db->getLastError();
	}

	public function secureString($string){//Secure a string for: SQL query (injection-safe)
		return $string;
		return $this->db->secureString($string);
	}

	public function getType(){
		return $this->db->getType();
	}

	public function __destruct(){
		return $this->db->__destruct();
	}

	public function beginTransaction(){
		return $this->db->beginTransaction();
	}

	public function commitTransaction(){
		return $this->db->commitTransaction();
	}

	public function rollbackTransaction(){
		return $this->db->rollbackTransaction();
	}

}

?>
