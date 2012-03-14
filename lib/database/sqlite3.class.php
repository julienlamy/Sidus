<?php
namespace SQL;

require_once __DIR__.'/db.interface.php';
/**
 * Database abstraction class
 */
class Database implements DBInterface{

	private $db;
	private $debug=true; //Display and throw error on each error
	private $profile=false; //Record every query

	public function __construct($db_link){ //Create the handler with the database
		$this->db=new \SQLite3($db_link);
		if($this->db == false){
			trigger_error('Can\'t connect to the database');
			exit;
		}
	}

	public static function test($db_link){ //Test the database
		try{
			$db=new SQLite3($db_link);
		}catch(Exception $e){
			return false;
		}
		return true;
	}

	public function exec($query){ //Execute a query
		$this->profile('exec', $query);
		if($this->db->exec($query) !== false){
			return true;
		}
		$this->debug('exec', $query);
		return false;
	}

	public function getSingle($query){ //Return the first value of the query
		$this->profile('getSingle', $query);
		$result=$this->db->querySingle($query);
		if($result !== false){
			return $result;
		}
		$this->debug('getSingle', $query);
		return false;
	}

	public function getRow($query){ //Return a row
		$this->profile('getRow', $query);
		$result=$this->db->querySingle($query, true);
		if($result !== false){
			return $result;
		}
		$this->debug('getRow', $query);
		return false;
	}

	public function getArray($query){ //Return an array for multiple rows
		$this->profile('getArray', $query);
		$result=$this->db->query($query);
		if($result !== false){
			$array=array();
			while($row=$result->fetchArray(SQLITE3_ASSOC)){
				$array[]=$row;
			}
			return $array;
		}
		$this->debug('getArray', $query);
		return false;
	}

	public function getLastId(){ //Get the last id inserted
		return $this->db->lastInsertRowID();
	}

	public function getLastError(){ //Get the last error
		return $this->db->lastErrorMsg();
	}

	public function secureString($string){//Secure a string for: SQL query (injection-safe), remove all HTML (XSS-safe)
		if($string !== null){
			return $this->db->escapeString($string);
		}else{
			return null;
		}
	}

	public function getType(){
		return 'sqlite3';
	}

	function __destruct(){
		return $this->db->close();
	}

	private function debug($label, $query){
		if($this->debug){
			trigger_error('<div style="position:absolute;background:red;width:900px;padding:10px;"><b>MySQL '.$label.' : </b>'.$query.'<br><b>Message : </b>'.$this->getLastError().'</div>');
		}
	}

	private function profile($label, $query){
		if($this->profile){
			file_put_contents('profile.sql', '--exec at '.date('c').':'."\n".$query."\n", FILE_APPEND);
		}
	}
	
	public function beginTransaction(){
		$this->exec('BEGIN TRANSACTION');
	}
	
	public function commitTransaction(){
		$this->exec('COMMIT TRANSACTION');
	}
	
	public function rollbackTransaction(){
		$this->exec('ROLLBACK');
	}

}
