<?php

namespace Sidus;

/**
 * Autoloading of classes 
 */
abstract class Loader{
	
	public static $classes = array();
	public static $paths = array();
	public static $extensions = array();
	
	/**
	 * Add include path to autoloader, either a relative path from REAL_PATH or
	 * an absolute path.
	 * @param string $path 
	 */
	public static function addPath($path){
		if(substr($path, 0 ,1) != DIRECTORY_SEPARATOR){
			$path = REAL_PATH.$path;
		}
		self::$paths[] = DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Add extension to autoloader like '.php' or '.class.php'
	 * @param string $extension 
	 */
	public static function addExtension($extension){
		self::$extensions[$extension] = $extension;
	}

	/**
	 * Register a class with a specific include path, this is the fastest way to
	 * autoload classes. You can either provide the full name of the classe,
	 * (with namespace) or just the local name.
	 * @param string $class_name
	 * @param string $include_path 
	 */
	public static function register($class_name, $include_path = null){
		if(substr($include_path, 0 ,1) != DIRECTORY_SEPARATOR){
			$include_path = REAL_PATH.$include_path;
		}
		self::$classes[$class_name] = $include_path;
	}
	
	/**
	 * Try to load a class from a class name, with either an absolute class path
	 * (with namespace) or just the local name.
	 * @param type $class_name
	 * @return null
	 * @throws \LogicException 
	 */
	public static function load($class_name){
		if(isset(self::$classes[$class_name]) && self::$classes[$class_name]){
			if(is_file(self::$classes[$class_name])){
				require_once self::$classes[$class_name];
				return;
			}
		}
		$full_name = explode('\\', $class_name);
		$class_name = array_pop($full_name);
		if(isset(self::$classes[$class_name]) && self::$classes[$class_name]){
			if(is_file(self::$classes[$class_name])){
				require_once self::$classes[$class_name];
				return;
			}
		}
		foreach(self::$paths as $path){
			foreach(self::$extensions as $extension){
				$file_path = $path.strtolower($class_name).$extension;
				if(is_file($file_path)){
					require_once $file_path;
					return;
				}
				$file_path = $path.$class_name.$extension;
				if(is_file($file_path)){
					require_once $file_path;
					return;
				}
			}
		}
		throw new \LogicException('Unable to load class : '.$class_name);
	}
}

spl_autoload_register(__NAMESPACE__.'\Loader::load'); //Add the autoloader to the PHP engine