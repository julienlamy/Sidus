<?php

namespace HTML;

class File extends Input {

	protected $types;
	protected $error_msg;
	protected $file;

	public function __construct($name, array $types = null, $not_null = false){
		parent::__construct($name);
		$this->type = 'file';
		$this->types = $types;
	}

	public function validate(){
		
		switch($this->file['error']){//Get errors from file object
			case UPLOAD_ERR_INI_SIZE:
				$this->error_msg = 'Le fichier dépasse la limite autoris&eacute;e.';
				return false;
			case UPLOAD_ERR_FORM_SIZE:
				$this->error_msg = 'Le fichier dépasse la limite autoris&eacute;e.';
				return false;
			case UPLOAD_ERR_PARTIAL:
				$this->error_msg = 'L\'envoi du fichier a été interrompu pendant le transfert.';
				return false;
			case UPLOAD_ERR_NO_FILE:
				$this->error_msg = 'Le fichier que vous avez envoyé a une taille nulle.';
				return false;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->error_msg = 'Répertoire temporaire introuvable.';
				return false;
			case UPLOAD_ERR_CANT_WRITE:
				$this->error_msg = 'Impossible d\'écrire dans le répertoire temporaire';
				return false;
			case UPLOAD_ERR_EXTENSION:
				$this->error_msg = 'Extension du fichier interdite';
				return false;
		}

		if(substr($this->file['name'], 0, 1) == '.'){//If the name begins with a .
			$this->error_msg = 'Nom de fichier non valide';
			return false;
		}

		$infos = pathinfo($this->file['name']); //Get infos on file
		if($infos['filename'] != ''){
			if($this->types != null){ //If a list of authorized extensions was specified
				if(!in_array(strtolower($infos['extension']), $this->types)){//Check if the file has the proper extension
					$this->error_msg = 'Type de fichier invalide';
					return false;
				}
			}
			if(preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $file['name'])){//Check the file name for illegal characters
				$this->error_msg = 'Nom de fichier non valide';
				return false;
			}
		} else {
			$this->error_msg = 'Aucun fichier spécifié.';
			return false;
		}
		return true;
	}

}