<?php

require_once 'class.ui_input.php';

class ui_file extends ui_input{

	protected $types;
	protected $dest;

	public function __construct($name, $label, $dest, array $types=null,$not_null=false){
		parent::__construct($name, $label);
		$this->types=$types;
		if(!is_dir(dirname($dest))){
			trigger_error('ABoard : Wrong destination folder: '.$dest.' for file input', E_USER_WARNING);
			exit;
		}
		$this->dest=$dest;
	}

	/**
	 * We need to do something about displaying user-input like that...
	 */
	public function display(){
		//$this->validate();
		$string=$this->display_label();
		$string.='<input type="file" name="'.$this->prefix.$this->name.'" id="'.$this->prefix.$this->name.'" class="'.$this->name.($this->error?' form_error':'').'" '.$this->attributes.'/>';
		$string.=$this->display_error();
		return $string;
	}

	/**
	 * Check and return the value or false if the user_input was incorrect.
	 */
	public function get_value($validate=true){
		if($this->value == null){//If no value was set, take the default one
			if(isset($_FILES[$this->prefix.$this->name])){//If a value is set by the user, take it
				$this->value=$_FILES[$this->prefix.$this->name];
			}
		}
		if(!$this->validated && $validate){
			$this->validate();
		}
		if($this->error){//Return false if there is an error
			return false;
		}
		return $this->value;
	}

	public function validate(){
		if($this->validated){
			return !$this->error;
		}
		$file=$this->get_value(false);
		if($file==null && !$this->not_null){
			$this->validated=true;
			return !$this->error;
		}

		switch($file['error']){//Get errors from file object
			case UPLOAD_ERR_INI_SIZE:
				$this->error=true;
				$this->error_msg='Le fichier dépasse la limite autoris&eacute;e.';
				return false;
			case UPLOAD_ERR_FORM_SIZE:
				$this->error=true;
				$this->error_msg='Le fichier dépasse la limite autoris&eacute;e.';
				return false;
			case UPLOAD_ERR_PARTIAL:
				$this->error=true;
				$this->error_msg='L\'envoi du fichier a été interrompu pendant le transfert.';
				return false;
			case UPLOAD_ERR_NO_FILE:
				if(!$not_null){
					$this->validated=true;
					return !$this->error;
				}
				$this->error=true;
				$this->error_msg='Le fichier que vous avez envoyé a une taille nulle.';
				return false;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->error=true;
				$this->error_msg='Répertoire temporaire introuvable.';
				return false;
			case UPLOAD_ERR_CANT_WRITE:
				$this->error=true;
				$this->error_msg='Impossible d\'écrire dans le répertoire temporaire';
				return false;
			case UPLOAD_ERR_EXTENSION:
				$this->error=true;
				$this->error_msg='Extension du fichier interdite';
				return false;
		}

		if(substr($file['name'], 0, 1) == '.'){//If the name begins with a .
			$this->error=true;
			$this->error_msg='Nom de fichier non valide';
			return false;
		}
		
		$infos=pathinfo($file['name']); //Get infos on file
		if($infos['filename']!=''){
			if($this->types != null){ //If a list of authorized extensions was specified
				if(!in_array(strtolower($infos['extension']), $this->types)){//Check if the file has the proper extension
					$this->error=true;
					$this->error_msg='Type de fichier invalide';
					return false;
				}
			}
			if(preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $file['name'])){//Check the file name for illegal characters
				$this->error=true;
				$this->error_msg='Nom de fichier non valide';
				return false;
			}
			if(!move_uploaded_file($file['tmp_name'], $this->dest)){//Move uploaded file to the specified destination
				$this->error=true;
				$this->error_msg='Impossible de déplacer le fichier.';
				return false;
			}
			$this->value['tmp_name']=$this->dest;
			$this->validated=true;
		} else {
			$this->error=true;
			$this->error_msg='Aucun fichier spécifié.';
			return false;
		}
		return !$this->error;
	}

}