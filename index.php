<?php

namespace Sidus;

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

require_once REAL_PATH.'lib/core/loader.class.php';
Loader::addExtension('.class.php');
Loader::addExtension('.interface.php');

Loader::addPath(REAL_PATH.'lib'.DIRECTORY_SEPARATOR.'core');
Loader::addPath(REAL_PATH.'lib'.DIRECTORY_SEPARATOR.'interface');
Loader::addPath(REAL_PATH.'lib'.DIRECTORY_SEPARATOR.'nodes');
Loader::addPath(REAL_PATH.'lib'.DIRECTORY_SEPARATOR.'properties');

Core::getInstance();
exit;
