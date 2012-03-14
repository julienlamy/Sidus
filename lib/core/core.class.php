<?php

namespace Sidus;

/**
 * Global core class.
 * This object is used with all classes
 * It contains everything
 *
 * @author Vincent Chalnot
 */
class Core{

	protected $db;
	protected $date;
	protected $error;
	protected $session;
	protected $user;
	protected $nodes = array();
	protected $groups = array();
	protected $infos = array();
	protected $nodes_types = array();
	protected $users = array();
	protected $controller;

	/**
	 * It loads the configuration from the config.ini
	 * then connect to the database and load the main parameters from node_data
	 */
	function __construct(){
		global $_core;
		$_core = $this;
		require_once REAL_PATH.'lib/nodes/node.class.php';
		$this->load_db();
		$this->load_config();
		$this->load_date();
		$this->load_error();
		$this->load_session();
		$this->load_user();
		$this->load_plugins();
		$this->load_controller();
	}

	public function __invoke(){
		return $this;
	}

	public static function getInstance(){
		global $_core;
		if($_core){
			return $_core;
		}
		return new self();
	}

	/**
	 * Load the database abstraction class from settings in config.ini
	 */
	private function load_db(){
		if(!file_exists(REAL_PATH.'secure/config.ini')){//Check the existence of an existing congig.ini file
			include REAL_PATH.'install/index.php';
			exit;
		}
		$db_conf = parse_ini_file(REAL_PATH.'secure/config.ini', 'database');
		$db_conf = $db_conf['database'];
		if(!isset($db_conf['type'])){
			trigger_error('ABoard : No database type in '.REAL_PATH.'secure/config.ini', E_USER_ERROR);
			exit;
		}
		if($db_conf['type'] == 'mysql'){//If MYSQL
			require_once REAL_PATH.'includes/class.db_mysql.php';
			$this->db = new sys_database($db_conf['host'].':'.$db_conf['port'], $db_conf['username'], $db_conf['password'], $db_conf['schema']);
		}elseif($db_conf['type'] == 'sqlite3'){//If SQLITE3
			require_once REAL_PATH.'includes/class.db_sqlite3.php';
			$this->db = new sys_database(REAL_PATH.$db_conf['database']);
		}else{
			trigger_error('ABoard : Database type not supported in '.REAL_PATH.'secure/config.ini', E_USER_ERROR);
			exit;
		}
	}

	/**
	 * Each application needs a different controller, as it will choose to
	 * redirect the user to the right page/view considering the action of
	 * the client. Still, you can you the prototype as a generic controller.
	 */
	protected function load_controller(){
		require_once REAL_PATH.'includes/class.proto_controller.php';
		$this->controller = new proto_controller();
	}

	/**
	 * TODO !!!!
	 * A lot of missing config options
	 */
	private function load_config(){
		$query = 'SELECT * FROM node_type';
		foreach($this->db->getArray($query) as $value){//Loading nodes types and setting the defines
			$this->nodes_types[$value['type_name']] = $value;
		}
		date_default_timezone_set($this->get('timezone'));
		define('TMP_DIRECTORY', $this->get('tmp_directory'));
		if(!is_writable(REAL_PATH.'secure')){//TODO : Drop a slightly more elegant error
			echo '<div style="position:absolute;background:red;width:60%;left:20%;padding:5px;top:9px;"><b>WARNING !</b> Secure directory is not writable:<br/>'.REAL_PATH.'secure/</div>';
		}
	}

	/**
	 * Load the date abstraction class
	 */
	private function load_date(){
		require_once REAL_PATH.'includes/class.sys_date.php';
		$this->date = new sys_date();
	}

	/**
	 * Load the error manager
	 */
	private function load_error(){
		require_once REAL_PATH.'includes/class.sys_error.php';
		$this->error = new sys_error();
	}

	/**
	 * Load the session manager from the error manager
	 */
	private function load_session(){
		require_once REAL_PATH.'includes/class.sys_session.php';
		$this->session = new sys_session();
	}

	/**
	 * Load the user handler
	 */
	private function load_user(){
		require_once REAL_PATH.'includes/nodes/class.node_user.php';
		$this->user = new node_user();
	}

	/**
	 * Load the plugins
	 */
	private function load_plugins(){
		$plugins_folders = REAL_PATH.'plugins/';
		$dir = dir($plugins_folders);
		$plugin = readdir($dir->handle);
		while($plugin != false){
			if(is_dir($dir->path.$plugin) && is_readable($dir->path.$plugin)){
				if(file_exists($dir->path.$plugin.'/index.php')){
					//include $dir->path.$plugin.'/index.php';
				}
			}
			$plugin = readdir($dir->handle);
		}
	}

	/**
	 * This is a special function to cache nodes in PHP memory
	 * Preventing DB overload
	 */
	public function node($id=null){
		if($id == null){
			return $this->controller->current_node();
		}
		$id = (int)$id;
		if(isset($this->nodes[$id])){//Checking if node already exists in the cache
			return $this->nodes[$id];
		}

		$query = 'SELECT type_name FROM node_generic WHERE node_id='.$id;
		$type = $this->db->getSingle($query); //Getting the type_name (and therefore the existence) of the node
		if($type == null){//If node doesn't exists
			$this->error->add(16);
			return false;
		}

		require_once $this->get_class_path($type);
		$class_name = $this->get_class_name($type);
		$this->nodes[$id] = new $class_name($id); //Dynamic class instanciation and node caching
		return $this->nodes[$id];
	}

	public final function get_class_name($type){
		if(!$this->is_type($type)){
			$this->error->add(0, 'Wrong type name !');
			return false;
		}
		return $this->nodes_types[$type]['class_name'];
	}

	public final function get_class_path($type){
		if(!$this->is_type($type)){
			$this->error->add(0, 'Wrong type name !');
			return false;
		}
		return REAL_PATH.$this->nodes_types[$type]['class_path'];
	}

	/**
	 * Return an array with all the nodes loaded in memory
	 */
	public function get_loaded_nodes(){
		return $this->nodes;
	}

	/**
	 * Check the existence of a node
	 */
	public function node_exists($id){
		$id = (int)$id;
		if(isset($this->nodes[$id])){
			return true;
		}
		$query = 'SELECT COUNT(*) FROM node_generic WHERE node_id='.$id;
		if($this->db->getSingle($query) == 1){
			return true;
		}
		return false;
	}

	/**
	 * Check if a type_name exists
	 */
	public final function is_type($type_name){
		if(array_key_exists($type_name, $this->nodes_types)){
			return true;
		}
		return false;
	}

	/**
	 * get infos about a type
	 */
	public final function getType($type_name){
		if(array_key_exists($type_name, $this->nodes_types)){
			return $this->nodes_types[$type_name];
		}
		return false;
	}

	/**
	 * Get the localized type of the node
	 */
	public final function get_localized_type($type_name){
		if($this->is_type($type_name)){
			return $this->localize($type_name);
		}
		//TODO Throw error
		return $this->localize('unknown');
	}

	public function get_thumb($type_name='generic'){
		$filename = 'generic.png';
		$real_path = REAL_PATH;
		$http_path = HTTP_PATH;
		$iconsd = $this->user->get_data('icons_directory');
		if(file_exists(REAL_PATH.$iconsd.$type_name.'.png')){
			$filename = $type_name.'.png';
		}
		if(file_exists(PROJECT_REAL_PATH.$iconsd.$type_name.'.png')){
			$filename = $type_name.'.png';
			$real_path = PROJECT_REAL_PATH;
			$http_path = PROJECT_HTTP_PATH;
		}
		list($width, $height) = getimagesize($real_path.$iconsd.$filename);
		$file = array(
			'filename' => $filename,
			'url' => $http_path.$iconsd.$filename,
			'path' => $real_path.$iconsd.$filename,
			'width' => $width,
			'height' => $height,
			'size' => filesize($real_path.$iconsd.$filename),
			'date' => filemtime($real_path.$iconsd.$filename)
		);
		return $file;
	}

	/**
	 * Get a global configuration variable from database
	 * @param <String> $key
	 * @return <String>
	 */
	public final function get($key){
		if(array_key_exists($key, $this->infos)){
			return $this->infos[$key];
		}
		if(!$this->is_set($key)){
			return false;
		}
		//TODO : Return correct type
		$query = 'SELECT data_value FROM node_data WHERE node_id=1 AND data_label=\''.$this->secureString($key).'\'';
		$this->infos[$key] = $this->db->getSingle($query);
		return $this->infos[$key];
	}

	/**
	 * Takes a string (generally from the db) and return the corresponding PHP type (or framework type)
	 * TODO: This is a mess
	 */
	public final function convert($value, $type='text'){
		switch($type){
			case 'array': return (array)unserialize($value);
			case 'boolean': return (bool)$value;
			case 'date' : return $this->secure_text($value); //Check the format
			case 'datetime' : return $this->secure_text($value); //Check the format
			case 'email' : return $this->secure_text($value); //Check the format
			case 'float' : return (float)$value;
			case 'html' : return $this->secure_html($value);
			case 'integer' : return (int)$value;
			case 'object': return (object)unserialize($value);
			case 'text' : return $this->secure_text($value);
			case 'time' : return $this->secure_text($value); //Check the format
			case 'timestamp' : return (int)$value;
			default : return $this->secure_text($value);
		}
	}

	/**
	 * Inverse of convert, takes a PHP variable and return a string.
	 * TODO: This is a mess
	 */
	public final function revert($value, $type){
		switch($type){
			case 'array': return serialize((array)$value);
			case 'boolean': if($value){
					return '1';
				}else{
					return '0';
				};
			case 'date' : return (string)$value; //Check the format
			case 'datetime' : return (string)$value; //Check the format
			case 'email' : return (string)$value; //Check the format
			case 'float' : return (string)(float)$value;
			case 'html' : return (string)$value;
			case 'integer' : return (string)(int)$value;
			case 'object': return serialize((object)$value);
			case 'text' : return (string)$value;
			case 'time' : return (string)$value; //Check the format
			case 'timestamp' : return (string)(int)$value;
			default : return (string)$value;
		}
	}

	public final function secureString($string, $allow_html=false){
		if($allow_html){
			return $this->db->secureString($this->secure_html($string));
		}
		return $this->db->secureString($this->secure_text($string));
	}

	public final function secure_text($string){
		return nl2br(htmlspecialchars($string, ENT_COMPAT, 'UTF-8'));
	}

	/**
	 * WARNING this function is not meant to secure user-input for the database, you need to use secureString($string, true); !!!
	 * @param <type> $string
	 * @return <type>
	 */
	public final function secure_html($string){
		return strip_tags($string, $this->get('safe_tags'));
	}

	/**
	 * Secure input to prevent XSS attacks
	 * This is just converting sensitive characters to their HTML equivalents
	 * @param (String) original
	 * @return (String) secured
	 */
	public function secure_display($string){
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Get a global configuration variable from database
	 * @param <String> $key
	 * @return <String>
	 */
	public function is_set($key){
		if(array_key_exists($key, $this->infos)){
			return true;
		}
		$query = 'SELECT COUNT(data_value) FROM node_data WHERE node_id=1 AND data_label=\''.$this->secureString($key).'\'';
		return (bool)$this->db->getSingle($query);
	}

	/**
	 * Set global configuration variable in database
	 * If key already exists, it will be overwritten.
	 * @param <String> $key
	 * @param <String> $value
	 * @return <Boolean>
	 */
	public function set($key, $value, $type='text'){
		$this->infos[$key] = $this->revert($value, $type);
		$query = 'INSERT INTO node_data (node_id,data_type,data_label,data_value) VALUES (1,\''.$type.'\{,\''.$this->secureString($key).'\',\''.$this->secureString($value).'\') ON DUPLICATE KEY UPDATE data_value=\''.$this->secureString($value).'\'';
		return $this->db->exec($query);
	}

	/**
	 * Get the db handler
	 * @return <Object>
	 */
	public function db(){
		return $this->db;
	}

	/**
	 * Get the date abstraction object
	 * @return <Object>
	 */
	public function date(){
		return $this->date;
	}

	/**
	 * Get the error handler
	 * @return <Object>
	 */
	public function error(){
		return $this->error;
	}

	/**
	 * Get the session
	 * @return <Object>
	 */
	public function session(){
		return $this->session;
	}

	/**
	 * Get the user
	 * @return <Object>
	 */
	public function user(){
		return $this->user;
	}

	/**
	 * Get the user
	 * @return <Object>
	 */
	public function controller(){
		return $this->controller;
	}

	public function localize($string){
		$file = REAL_PATH.'includes/localization/general.'.$this->user()->get_data('lang').'.php';
		if(file_exists($file)){
			include $file;
			if(isset($local[$string])){//Test with exact case (for accronyms)
				return $local[$string];
			}
			$lowercase = strtolower($string);
			if(isset($local[$lowercase])){
				if($lowercase != $string){//We are just taking in account the capitalization of the first letter
					return ucfirst($local[$lowercase]);
				}
				return $local[$lowercase];
			}
		}
		return $string;
	}

	/**
	 * This function tries to gracefully shorten titles and short strings to
	 * the specified number of characters.
	 */
	public function shorten($string, $len=40){
		$tmp = explode(',', $string);
		$string = trim($tmp[0]);
		if(strlen($string) > $len){
			$tmp = substr($string, 0, $len + 1);
			$cut = strrpos($tmp, ' ');
			$tmp = substr($tmp, 0, $cut);
			if($tmp == ''){
				$tmp = substr($string, 0, $len);
			}
			return trim($tmp).'&hellip;';
		}
		return $string;
	}

	/**
	 * This function tries to gracefully shorten a long text to the specified
	 * number of characters.
	 */
	public function summarize($string, $len=300){
		if(strlen($string) > $len){
			$tmp = substr($string, 0, $len + 1);
			$cut = strrpos($tmp, '.', -1);
			$cut2 = strrpos($tmp, ',', -1);
			if($cut2 > $cut){
				$cut = $cut2;
			}
			$tmp = substr($tmp, 0, $cut);
			if($tmp == ''){
				$tmp = substr($string, 0, $len);
			}
			return trim($tmp).'&hellip;';
		}
		return $string;
	}

	/**
	 * You should use a common function to generate URLs to the nodes
	 */
	public function link($node, $options=array()){
		$link = PROJECT_HTTP_PATH.'index.php?node_id='.$node->get('node_id');
		if(!is_array($options)){
			$options = (string)$options;
			if($options != ''){
				$link.='&'.$options;
			}
			return $link;
		}
		foreach($options as $value){
			$link.='&'.$value;
		}
		return $link;
	}

	public static function slugify($string){
		$text = self::replace_non_ascii('-', $string); // replace non letter or digits by -
		$text = trim($text, '-');
		if(function_exists('iconv')){
			$text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // transliterate
		}
		$text = strtolower($text);
		$text = preg_replace('~[^-\w]+~', '', $text); // remove unwanted characters
		if(empty($text)){
			return 'n-a';
		}
		return $text;
	}

	public static function replace_non_ascii($subject, $replacement){
		return preg_replace('~[^\\pL\d]+~u', $replacement, $subject);
	}

	public function generate_tags_links(node_generic $node){
		$str = '';
		if(!$node->get_auth('read')){
			return $str;
		}
		$tags = $this->replace_non_ascii(' ', $node->get('tags')); // replace non letter or digits by " "
		$tags = trim($tags, ' ');
		$tags = explode(' ', $tags);
		foreach($tags as $tag){
			if($tag != ' ' || $tag != ''){
				$str.=' <a href="?search='.urlencode($tag).'">'.$tag.'</a> ';
			}
		}
		return $str;
	}

	public function convert_to_text($html, $cut=null){
		if($cut == null){
			return html_entity_decode(strip_tags($html));
		}
		return $this->summarize(html_entity_decode(strip_tags($html)), $cut);
	}

	public static final function get_attributes_from_array(array $array){
		$str = '';
		foreach($array as $key => $value){
			if(is_array($value)){
				$value = implode(' ', $value);
			}
			$str.=$key.'="'.$value.'" ';
		}
		return $str;
	}

}