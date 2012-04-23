<?php

class Utils{

	public static function slugify($string, $delimiter = '-'){
		$text = self::replaceControlChars($string, $delimiter); // replace non letter or digits by the delimiter
		$text = trim($text, $delimiter);
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

	public static function replaceControlChars($subject, $replacement){
		return preg_replace('~[^\\pL\d]+~u', $replacement, $subject);
	}

	public static function camelize($string){
		$string = ucwords(self::replaceControlChars($string, ' '));
		$string = str_replace(' ', '', $string);
		return $string;
	}

	public function convertToText($html, $cut = null){
		if($cut == null){
			return html_entity_decode(strip_tags($html));
		}
		return $this->summarize(html_entity_decode(strip_tags($html)), $cut);
	}

	public static final function getAttributesFromArray(array $array){
		$str = '';
		foreach($array as $key => $value){
			if(is_array($value)){
				$value = implode(' ', $value);
			}
			$str.=$key.'="'.$value.'" ';
		}
		return $str;
	}

	/**
	 * This function tries to gracefully shorten titles and short strings to
	 * the specified number of characters.
	 */
	public function shorten($string, $len = 40){
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
	public function summarize($string, $len = 300){
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
	 * Secure input to prevent XSS attacks
	 * This is just converting sensitive characters to their HTML equivalents
	 * @param (String) original
	 * @return (String) secured
	 */
	public function secureDisplay($string){
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * This is just converting sensitive characters to their HTML equivalents
	 * @param (String) original
	 * @return (String) secured
	 */
	public final function secureText($string){
		return nl2br(htmlspecialchars($string, ENT_COMPAT, 'UTF-8'));
	}

}

?>
