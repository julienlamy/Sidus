<?php
/**
 * Installation script, just a quick dirty script to setup the framework
 */

//Set error reporting on:
error_reporting(E_ALL);
ini_set('display_errors','true');

//Lets use the form layer class form the framework to display stuff
require_once REAL_PATH.'lib/interface/form.class.php';
require_once REAL_PATH.'lib/interface/input.class.php';
require_once REAL_PATH.'lib/interface/select.class.php';
require_once REAL_PATH.'lib/interface/textarea.class.php';

class framework_install{

	//Array for error messages
	protected $errors=array();
	protected $install_error=true;
	protected $db;
	protected $config_path;
	protected $form;

	function __construct(){
		$this->config_path=REAL_PATH.'secure/config.ini';
		
		//If config file exists, go to parent directory (this means there is a problem somewhere as this script shouldn't be loaded)
		if(file_exists($this->config_path)){//Check the existence of an existing congig.ini file
			header('Location: '.dirname(HTTP_PATH));
			exit;
		}

		if(!is_writable(REAL_PATH) && !file_exists(REAL_PATH.'secure')){
			$this->errors[]='Framework directory is not writable but it\'s OK, you must create a directory named "secure" inside the framework directory. ('.REAL_PATH.')';
		}
		if(!file_exists(REAL_PATH.'secure')){//If secure folder doesn't already exists
			if(!@mkdir(REAL_PATH.'secure')){//Try to create directory
				$this->errors[]='Unable to create the secure folder in '.REAL_PATH.'.'; //If error while creating secure folder
			}
		} else {//If secure directory already exists, check if writable
			if(!is_writable(REAL_PATH.'secure')){
				$this->errors[]='Secure directory is not writable. ('.REAL_PATH.'secure/)';
			}
		}
		if(!file_exists(REAL_PATH.'secure/.htaccess')){
			if(!@file_put_contents(REAL_PATH.'secure/.htaccess', "order allow,deny\ndeny from all\nallow from localhost, 127.0.0.1\n")){//Try to write .htaccess
				$this->errors[]='Unable to create .htaccess file in '.REAL_PATH.'secure.';
			}
		}

		$this->form=new ui_form();

		$this->form->add(new Select('database_type', 'Type : ', array('mysql'=>'MySQL', 'sqlite3'=>'SQLite 3.X'), 'mysql'));
		$this->form->get('database_type')->set_html_attributes('onchange="select_options()"');
		if($this->form->getValue('database_type') == 'mysql'){
			$b=true;
		}else{
			$b=false;
		}
		$this->form->add(new Input('database_host', 'Host:', 'text', 'localhost', $b));
		$this->form->add(new Input('database_username', 'Username:', 'text', 'root', $b));
		$this->form->add(new Input('database_password', 'Password:', 'password'));
		$this->form->add(new Input('database_port', 'Port:', 'text', '3306', $b));
		$this->form->get('database_port')->set_regexp('/^\d*$/');
		$this->form->add(new Input('database_schema', 'Database:', 'text', 'aboard', $b));
		$this->form->add(new Input('username', 'Username:', 'text', 'Admin', true));
		$this->form->add(new Input('email', 'Email:', 'text', 'admin@'.$_SERVER['SERVER_NAME'], true));
		$this->form->get('email')->setRegexp('/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/');
		$this->form->get('email')->setErrorMessage('This is not a correct email address.');
		$this->form->add(new Input('temp1', 'Password:', 'password'));
		$this->form->add(new Input('temp2', 'Confirm:', 'password'));
		$this->form->add(new Input('sha1', null, 'hidden', sha1(''), true));
		$this->form->get('sha1')->setErrorMessage('You need to enter a password.');
	}
	
	public function install(){
		if($this->form->validate()){//Essentials informations
			if($this->form->getValue('database_type') == 'mysql'){//If MYSQL
				if($this->mysqlInstall()){
					$this->install_error=false;
				}
			}elseif($this->form->getValue('database_type') == 'sqlite3'){//If SQLite 3
				if($this->sqlite3Install()){
					$this->install_error=false;
				}
			}else{
				$this->errors[]='The following database: "'.$this->form->getValue('database_type').'" is not yet supported.';
			}
		}
	}


	protected function genericInstall($script){
		$script=explode(';', $script);
		foreach($script as $query){
			$query=trim($query);
			if($query != ''){
				if(!$this->db->exec($query)){
					$this->errors[]='Error during installation: '.$this->db->getLastError();
					$this->genericUninstall(file_get_contents(REAL_PATH.'includes/installation/uninstall.sql'));
					return false;
				}
			}
		}
		$query='UPDATE node_generic SET title=\''.$this->db->secureInput($this->form->getValue('username')).'\', creator=\''.$this->db->secureInput($this->form->getValue('username')).'\' WHERE node_id=7';
		if(!$this->db->exec($query)){
			$this->errors[]='Error during installation: '.$this->db->getLastError();
			$this->genericUninstall(file_get_contents(REAL_PATH.'includes/installation/uninstall.sql'));
			return false;
		}
		
		$salt='';
		$possible='23456789ABCDEFGHJKLMNPQRSTVWXYZabcdefghijkmnpqrstvwxyz';
		$i=0;
		while($i < 10){
			$char=substr($possible, mt_rand(0, strlen($possible) - 1), 1);
			$salt.=$char;
			$i++;
		}
		$salt=sha1($salt);
		$query='UPDATE node_user SET username=\''.$this->db->secureInput($this->form->getValue('username')).'\', email=\''.$this->db->secureInput($this->form->getValue('email')).'\', password=\''.sha1($this->form->getValue('sha1').$salt).'\', salt=\''.$salt.'\' WHERE node_id=7';
		if(!$this->db->exec($query)){
			$this->errors[]='Error during installation: '.$this->db->getLastError();
			$this->genericUninstall(file_get_contents(REAL_PATH.'includes/installation/uninstall.sql'));
			return false;
		}
		$query='UPDATE node_generic SET creator=\''.$this->db->secureInput($this->form->getValue('username')).'\' WHERE node_id=1 OR node_id=2 OR node_id=3 OR node_id=4';
		if(!$this->db->exec($query)){
			$this->errors[]='Error during installation: '.$this->db->getLastError();
			$this->genericUninstall(file_get_contents(REAL_PATH.'includes/installation/uninstall.sql'));
			return false;
		}
		return true;
	}

	protected function genericUninstall($script){
		$script=explode(';', $script);
		foreach($script as $query){
			$query=trim($query);
			if($query != ''){
				$this->db->exec($query);
			}
		}
		if(file_exists($this->config_path)){
			unlink($this->config_path);
		}
		$this->errors[]='Error during installation, aborting.';
	}

	protected function mysqlInstall(){
		if($this->form->getValue('database_host') === false || $this->form->getValue('database_username') === false || $this->form->getValue('database_password') === false || $this->form->getValue('database_port') === false || $this->form->getValue('database_schema') === false){
			$this->errors[]='Error ! Some values are missing.';
			return false;
		}
		require_once REAL_PATH.'includes/class.db_mysql.php';
		$db_tmp=sys_database::test($this->form->getValue('database_host').':'.$this->form->getValue('database_port'), $this->form->getValue('database_username'), $this->form->getValue('database_password'), $this->form->getValue('database_schema'));
		if($db_tmp !== true){
			$this->errors[]='Error ! Can\'t access the database: '.$db_tmp;
			return false;
		}
		$this->db=new sys_database($this->form->getValue('database_host').':'.$this->form->getValue('database_port'), $this->form->getValue('database_username'), $this->form->getValue('database_password'), $this->form->getValue('database_schema'));
		$success=$this->genericInstall(file_get_contents(REAL_PATH.'includes/installation/mysql.sql'));
		if(!$success){
			$this->errors[]='Error ! There was a problem during installation.';
			return false;
		}
		if(!file_put_contents($this->config_path, ';config.ini'."\n".
				';Access must be forbidden by the user !!!'."\n".
				"\n".
				'[database]'."\n".
				'type=mysql'."\n".
				'host="'.addcslashes($this->form->getValue('database_host'), '"').'"'."\n".
				'port='.$this->form->getValue('database_port')."\n".
				'username="'.addcslashes($this->form->getValue('database_username'), '"').'"'."\n".
				'password="'.addcslashes($this->form->getValue('database_password'), '"').'"'."\n".
				'schema='.$this->form->getValue('database_schema')."\n")){
			$this->errors[]='Error ! Can\'t create the config file: '.$this->config_path;
			return false;
		}
		return true;
	}

	protected function sqlite3Install(){
		require_once REAL_PATH.'includes/class.db_sqlite3.php';
		$db_path='secure/sqlite3.database.db';
		$db_tmp=sys_database::test(REAL_PATH.$db_path);
		if($db_tmp !== true){
			$this->errors[]='Error ! Can\'t create the database: '.$db_tmp;
			return false;
		}
		$this->db=new sys_database(REAL_PATH.$db_path);
		$success=$this->genericInstall($this->convertToSQLite(file_get_contents(REAL_PATH.'includes/installation/mysql.sql')));
		if(!$success){
			return false;
		}
		if(!file_put_contents($this->config_path, ';config.ini'."\n".
				';Access must be forbidden by the user !!!'."\n".
				"\n".
				'[database]'."\n".
				'type=sqlite3'."\n".
				'database='.$db_path."\n")){
			$this->errors[]='Error ! Can\'t create the config file: '.$this->config_path;
			return false;
		}
		return true;
	}
	
	public function isInstalled(){
		if(count($this->errors)>0){
			return false;
		}
		if($this->install_error){
			return false;
		}
		return true;
	}
	
	public function getErrors(){
		return $this->errors;
	}
	
	public function form(){
		return $this->form;
	}
	
	public function convertToSQLite($script){
		$script=str_replace('SET storage_engine=MYISAM;', '', $script);
		$script=str_replace('SET storage_engine=InnoDB;', '', $script);
		$script=str_replace(' AUTO_INCREMENT', '', $script);
		$script=str_replace('NOW()', '\''.date('c').'\'', $script);
		return $script;
	}
}

$install=new framework_install();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Installation</title>
		<style type="text/css">
			html{width:100%;height:100%;margin:0;padding:0;background:#333;font-family:"Trebuchet MS"}
			body{width:750px;min-height:100%;margin:auto;padding:50px;background:#fff;-webkit-box-shadow:#888 0 0 60px;}
			h1{margin:0;padding:10px 30px 0;color:#333;text-align:right;}
			h2{background:#eee;color:#444;font-size:20px;padding:5px 30px;}
			label{display:inline-block;width:150px;}
			select{width:200px;padding:1px 0;}
			p{font-size:14px;padding:10px;margin:0;color:#666;}
			input{width:200px;}
			.version{margin:0;padding:10px 30px 10px;color:#999;text-align:right;}
			.section{padding:20px 0 0;}
			.section .block{display:inline-block;width:360px;vertical-align:top;}
			.section .block.left{margin-right:25px;}
			.error, #passwd_error, .form_error{color:red;}
			input.form_error{border-color:red;}
			#classic_database{opacity:0;-webkit-transition: opacity 0.5s linear;}
			.submit{display:inline-block;border:1px solid #bbb;padding:5px 20px;background:#fff;cursor:pointer;}
			.submit:hover{background:#333;color:#fff;}
		</style>
	</head>
	<body>
		<h1>A-BOARD FRAMEWORK INSTALLATION</h1>
		<div class="version">Version 1.0.1 <i>"Graphite"</i> July 2011</div>
		<noscript><p class="error">You can't install this framework without JavaScript !</p></noscript>
		<?php if(isset($_POST['database_type'])) $install->install() ?>
<?php if($install->isInstalled()): ?>
			<div class="section">
				<div class="error" style="color:green">Installation réussie !</div>
				<h2 style="text-align:right"><input type="button" onclick="window.location.reload()" name="continue" value="Continue" class="submit" /></h2>
			</div>
		</body>
	</html>
	<?php exit ?>
<?php endif ?>

<form action="" method="post" onsubmit="return checkpass()">
	<?php $errors=$install->getErrors() ?>
	<?php if(count($errors) > 0): ?>
		<div class="section">
			<h2>The following errors occured:</h2>
			<?php foreach($errors as $value): ?>
				<p class="error"><?php echo $value ?></p>
			<?php endforeach ?>
		</div>
		<?php exit ?>
	<?php endif ?>

	<div class="section">
		<h2>Database informations:</h2>
		<div class="block left">
			<?php $install->form()->display('database_type') ?>
			<p>Choose carefully your database type, you won't be able to switch to another one after installation. If you don't know what to choose, try SQLite 3.x.</p>
			<script type="text/javascript">
				function select_options(){
					obj=document.getElementById('database_type');
					if(obj.selectedIndex==0){
						document.getElementById('classic_database').style.opacity=1;
					} else {
						document.getElementById('classic_database').style.opacity=0;
					}
				}
			</script>
		</div>

		<div class="block" id="classic_database">
			<?php
			$install->form()->display('database_host');
			$install->form()->display('database_username');
			$install->form()->display('database_password');
			$install->form()->display('database_port');
			$install->form()->display('database_schema');
			?>
		</div>
	</div>

	<div class="section">
		<h2>Administrator account:</h2>
		<div class="block left">
			<?php
			$install->form()->display('username');
			$install->form()->display('email');
			$install->form()->display('temp1');
			$install->form()->display('temp2');
			$install->form()->display('sha1');
			?>
			<div id="passwd_error"></div>
		</div>
		<div class="block">
			<p>The administrator account is a special account, it has every permissions in the whole board, and it can't be deleted. Be very careful with this, it can be compared with the root account on linux.</p>
		</div>

	</div>

	<div class="section">
		<?php if(!$install->isInstalled()){ ?>
			<h2 style="text-align:right"><input type="button" onclick="window.location.reload()" name="reset" value="Reset" class="submit" /> <input type="submit" name="submit" value="Submit" class="submit" /></h2>
		<?php } ?>
	</div>

</form>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>scripts/login.js"></script>
<script type="text/javascript">select_options()</script>
</body>
</html>
