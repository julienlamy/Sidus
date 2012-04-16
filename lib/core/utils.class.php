<?php
class Utils {
	static public function camelize($key){
		$tmp=explode('_',$key);
		$tmp2='';
		foreach($tmp as $value){
			$tmp2.=$value;
		}
		return $tmp2;
	}
}
?>
