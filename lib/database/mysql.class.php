<?php
namespace SQL;

require_once __DIR__.'/db.interface.php';

/**
* Database abstraction class
*/
class Database implements DBInterface{

	private $db;
	private $debug=true;//Display and throw error on each error
	private $profile=false;//Record every query

	public function __construct($host, $user, $passwd, $database){ //Create the handler with the database
		$this->db=mysql_pconnect($host, $user, $passwd);
		if($this->db !== false){
			if(!mysql_select_db($database, $this->db)){
				trigger_error('Database '.$database.' doesn\'t exists.');
				exit;
			}
		} else{
			trigger_error('Can\'t connect to the database');
			exit;
		}
	}
	
	public static function test($host, $user, $passwd, $database){ //Test the database
		$db=@mysql_pconnect($host, $user, $passwd);
		if($db !== false){
			if(!mysql_select_db($database, $db)){
				return 'Database '.$database.' doesn\'t exists.';
			} else {
				return true;
			}
		}
		return 'Can\'t connect to the database';
	}

	public function exec($query){ //Execute a query
		$this->profile('exec', $query);
		if(mysql_query($query, $this->db) !== false){
			return true;
		}
		$this->debug('exec', $query);
		return false;
	}

	public function getSingle($query){ //Return the first value of the query
		$this->profile('getSingle', $query);
		$result = mysql_query($query, $this->db);
		if($result !== false){
			$tmp = mysql_fetch_row($result);
			return $tmp[0];
		}
		$this->debug('getSingle', $query);
		return false;
	}

	public function getRow($query){ //Return a row
		$this->profile('getRow', $query);
		$result = mysql_query($query, $this->db);
		if($result !== false){
			$tmp = mysql_fetch_array($result, MYSQL_ASSOC);
			return $tmp;
		}
		$this->debug('getRow', $query);
		return false;
	}

	public function getArray($query){ //Return an array for multiple rows
		$this->profile('getArray', $query);
		$result = mysql_query($query, $this->db);
		if($result !== false){
			$array = array();
			while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
				$array[] = $row;
			}
			return $array;
		}
		$this->debug('getArray', $query);
		return false;
	}

	public function getLastId(){ //Get the last id inserted
		return mysql_insert_id($this->db);
	}

	public function getLastError(){ //Get the last error
		return mysql_error($this->db);
	}

	public function secureString($string){//Secure a string for: SQL query (injection-safe)
		if($string !== null){
			return mysql_real_escape_string($string, $this->db);
		}else{
			return null;
		}
	}

	public function getType(){
		return 'mysql';
	}

	public function __destruct(){
		return mysql_close($this->db);
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
		$this->exec('START TRANSACTION');
	}

	public function commitTransaction(){
		$this->exec('COMMIT');
	}

	public function rollbackTransaction(){
		$this->exec('ROLLBACK');
	}
}
