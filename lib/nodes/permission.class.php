<?php

namespace Sidus\Nodes;

class Permission extends Node {
	const READ = 1;
	const WRITE = 2;
	const ADD = 4;
	const DELETE = 8;
	const OWNER = 31;
	const ADMIN = 47;
	const ALL = 63;
	const INVERSE = 64;
	const NOT_PERMITTED = 127;

	public static function checkAuth($auth, $permission = self::READ){
		return ($auth & $permission) == $permission;
	}
	
}