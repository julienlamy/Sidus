<?php

if($_SERVER['SCRIPT_FILENAME'] == __FILE__){
	include 'copyright.html';
	exit;
}

if(!defined('REAL_PATH')){
	define('REAL_PATH', __DIR__.DIRECTORY_SEPARATOR);
}
if(!defined('PROJECT_REAL_PATH')){
	define('PROJECT_REAL_PATH', trim(dirname($_SERVER['SCRIPT_FILENAME']),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
}

function Core(){
	return Core::getInstance();
}

require_once REAL_PATH.'/lib/core/core.class.php';
