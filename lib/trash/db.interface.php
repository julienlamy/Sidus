<?php

namespace SQL;

interface DBInterface{

	public function exec($query);

	public function getSingle($query);

	public function getRow($query);

	public function getArray($query);

	public function getLastId();

	public function getLastError();

	public function secureString($string);

	public function getType();

	public function beginTransaction();

	public function commitTransaction();

	public function rollbackTransaction();
}

?>
