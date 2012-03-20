<?php

namespace Sidus\Database;

/**
 * Description of database
 *
 * @author vincent
 */
class Database extends \PDO{
	
	private $debug=true;//Display and throw error on each error
	private $profile=false;//Record every query


	public function exec($statement){ //Execute a query
		$this->profile('exec', $statement);
		$result = parent::exec($statement);
		$this->debug(__METHOD__, $statement);
		return $result;
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

	public function __destruct(){
		return mysql_close($this->db);
	}

	private function debug($method, $query){
		if($this->debug){
			trigger_error('<div style="position:absolute;background:red;width:900px;padding:10px;"><b>PDO::'.$method.'() : </b>'.$query.'<br><b>Message : </b>'.$this->getLastError().'</div>');
		}
	}

	private function profile($label, $query){
		if($this->profile){
			file_put_contents('profile.sql', '--exec at '.date('c').':'."\n".$query."\n", FILE_APPEND);
		}
	}

}

?>
