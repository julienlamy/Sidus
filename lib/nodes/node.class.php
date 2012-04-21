<?php

namespace Sidus\Nodes;

use Sidus\Core;
use Sidus\Database;

class Node {

	protected $auths; //Array with the authorizations informations
	protected $properties; //Node informations (node_id, title, etc...)
	protected $public = array('node_id', 'parent_node_id'); //Public keys for the get() method
	protected $single_error = array('read' => true, 'add' => true, 'edit' => true, 'delete' => true); //This is meant to prevent repeating the same error message
	protected $perms; //Array containing the permissions for this node
	protected $form = array(); //The form to edit the content of the node
	protected $autosave = true;
	protected $select_statement;
	protected $insert_statement;
	protected $delete_statement;

	public function __construct(){

	}

	public function __call($name, $arguments){
		return Core::$name($this, $arguments);
	}

	public function __destruct(){
		if($this->autosave){
			$this->save();
		}
	}

	public function save($new = false){
		if(!$this->id || $new){
			$this->saveNew();
		}
		$to_save = array();
		foreach($this->properties as $property){
			if(!$property->hasChanged()){
				continue;
			}
			if(!isset($to_save[$property->getTableName()])){
				$to_save[$property->getTableName()] = array();
			}
			$to_save[$property->getTableName()][$property->getColumnName()] = $property;
		}
		$db = Database::getInstance();
		$db->beginTransaction();
		foreach($to_save as $table_name => &$columns){
			$query = "UPDATE FROM $table_name SET ";
			foreach($columns as $column_name => $property){
				$query .= "$column_name = :$column_name ";
			}
			$query .= "WHERE node_id = {$this->id}";
			$stmt = $db->prepare($query);
			foreach($columns as $column_name => $property){
				$stmt->bindValue(":$column_name", $property->toDB(), $property->getPDOParam());
			}
			try {
				$stmt->execute();
			} catch(PDOException $e){
				$db->rollBack();
				$error = $db->errorInfo();
				throw new NodeSaveException("Unable to save node : {$error[2]}");
			}
		}
		$db->commit();
	}

	protected function hydrate(array $line = null){
		if($line){
			$properties = $this->properties;
			foreach($properties as $key => $property){
				$full_column_name = $property->getFullColumnName();
				if(isset($line[$full_column_name]) && $property->check($line[$full_column_name])){
					$property->set($line[$full_column_name]);
					$property->reset();
					unset($properties[$key]);
				}
			}
			if(count($properties) == 0){
				return true;
			}
			$missing = array();
			foreach($properties as $property){
				$missing[] = $property->getFullColumnName();
			}
			throw new NodeHydrateException("Hydratation failed : Missing properties are ".implode(', ', $missing));
		}
		$statement = $this->getSelectStatement();
		$statement->bindValue(':node_id', $this->id, \PDO::PARAM_INT);
		$statement->execute();
		$this->hydrate($statement->fetch(PDO::FETCH_ASSOC));
	}

	protected function getSelectStatement(){
		if(self::$select_statement){
			return self::$select_statement;
		}
		$tables = array();
		$columns = array();
		foreach($this->properties as $property){
			$tables[$property->getTableName()] = $property->getTableName();
			$columns[] = $property->getFullColumnName();
		}
		$tables = implode(',', $tables);
		$columns = implode(',', $columns);
		$query = "SELECT $columns FROM $tables WHERE node_id = :node_id";
		self::$select_statement = Database::getInstance()->prepare($query);
		return self::$select_statement;
	}

	protected function getInsertStatement(){
		if(self::$insert_statement){
			return self::$insert_statement;
		}
		$tables = array();
		$columns = array();
		foreach($this->properties as $property){
			$tables[$property->getTableName()] = $property->getTableName();
			$columns[] = $property->getFullColumnName();
		}
		//$query = "INSERT ".implode(',', $columns);
		//$query .= " FROM ".implode(',', $tables)." WHERE node_id = :node_id";
		//TODO
		self::$insert_statement = Database::getInstance()->prepare($query);
		return self::$insert_statement;
	}

	protected function getDeleteStatement(){
		if(self::$delete_statement){
			return self::$delete_statement;
		}
		$tables = array();
		$columns = array();
		foreach($this->properties as $property){
			$tables[$property->getTableName()] = $property->getTableName();
			$columns[] = $property->getFullColumnName();
		}
		$query = "DELETE FROM ".implode(',', $tables)." WHERE node_id = :node_id";
		self::$delete_statement = Database::getInstance()->prepare($query);
		return self::$delete_statement;
	}

	/**
	 * This function initialize the node, it gets the basic informations out of the database,
	 * then it checks the permissions and some minor stuff.
	 * You can rewrite this constructor
	 * Don't forget to use parent::__construct()
	 */
//	public function __construct($id){
//		$id=(int)$id;
//
//		//Query basic informations
//		$query='SELECT * FROM node WHERE node_id='.$id;
//		if($this->properties===null){
//			$this->properties=_core()->db()->getRow($query);
//		} else {
//			$this->properties=array_merge($this->properties, _core()->db()->getRow($query));
//		}
//		
//		if($this->properties == false){//If the node doesn't exists in the database
//			trigger_error('ABoard : Node '.$id.' doesn\'t exists in database.', E_USER_ERROR);
//			exit;
//		}
//
//		$this->auths=$this->getAuthorizations(); //Getting all the authorizations
//
//		//Check ownership on current node
//		//
//		//If the user has the ownership
//		if($this->auths['ownership']){
//			if(!$this->properties['inherit_permissions']){//and the permissions are NOT inherited
//				$this->auths['read']=true;
//				$this->auths['add']=true;
//				$this->auths['edit']=true;
//				$this->auths['delete']=true;
//			} else {//else this means the ownership is corrupted by heritage
//				$this->auths['ownership']=false;
//				if(_core()->user()!=null){//If user is already initialized
//					if(_core()->user()->isConnected()){//if the user is connected
//						if($this->is_owner(_core()->user())){//This means the user is really the owner of the current node
//							$this->auths['ownership']=true;
//							$this->auths['read']=true;
//							$this->auths['add']=true;
//							$this->auths['edit']=true;
//							$this->auths['delete']=true;
//						}
//					}
//				}
//			}
//		}
//	}

	/**
	 * This function checks the permissions on the node and return an array with
	 * the corresponding permissions.
	 * We never check the ownership because we need this function for recursive tasks
	 */
	public final function getAuthorizations(){
		//If the permissions of the node are already loaded, then returns the cache.
		if($this->auths != null){
			return $this->auths;
		}

		//If this node has inherited permissions, return the parent node's permissions
		if($this->properties['inherit_permissions']){
			$auths = $this->getParent()->getAuthorizations();
			//If the user is not initialized, returns current permissions
			if(_core()->user() == null){
				return $auths;
			}
			//If the user is not connected, returns the permissions right now.
			if(!_core()->user()->isConnected()){
				return $auths;
			}
			//Checking for each permissions/subsriptions ONLY FOR ONWERSHIP OR MASTERSHIP (+positive filter)
			foreach($this->getPermissions() as $perm){
				//If user is in permission or in group's permission
				if(_core()->user()->isInGroup($perm['entity_id']) || $perm['entity_id'] == _core()->user()->get('node_id')){
					if(!$perm['b_inverse']){//If permissions are not inversed (meaning they are normal)
						//Checking individual permissions
						if($perm['b_ownership']){
							$auths['ownership'] = true;
						}
						if($perm['b_mastership']){
							$auths['mastership'] = true;
							$auths['read'] = true;
							$auths['add'] = true;
							$auths['edit'] = true;
							$auths['delete'] = true;
						}
					}
				}
			}
			return $auths; //Will return parent's permissions + ownership or mastership of current node if exists
		}

		//Let's start with everything to false by default.
		$auths = array('read' => false, 'add' => false, 'edit' => false, 'delete' => false, 'ownership' => false, 'mastership' => false); //Init values
		//
		//Get anonymous authorizations
		$auths['read'] = (bool)$this->properties['anonymous_read'];
		$auths['add'] = (bool)$this->properties['anonymous_add'];
		$auths['edit'] = (bool)$this->properties['anonymous_edit'];
		$auths['delete'] = (bool)$this->properties['anonymous_delete'];

		//If the user is not initialized, returns current permissions
		if(_core()->user() == null){
			return $auths;
		}
		//If the user is not connected, returns the permissions right now.
		if(!_core()->user()->isConnected()){
			return $auths;
		}

		//Checking for each permissions/subsriptions (+positive filter)
		foreach($this->getPermissions() as $perm){
			//If user is in permission or in group's permission
			if(_core()->user()->isInGroup($perm['entity_id']) || $perm['entity_id'] == _core()->user()->get('node_id')){
				if(!$perm['b_inverse']){//If permissions are not inversed (meaning they are normal)
					//Checking individual permissions
					if($perm['b_read'])
						$auths['read'] = true;
					if($perm['b_add'])
						$auths['add'] = true;
					if($perm['b_edit'])
						$auths['edit'] = true;
					if($perm['b_delete'])
						$auths['delete'] = true;
					if($perm['b_ownership'])
						$auths['ownership'] = true;
					if($perm['b_mastership']){
						$auths['mastership'] = true;
					}
				}
			}
		}

		//Checking for each permissions/subsriptions (-NEGATIVE filter)
		foreach($this->getPermissions() as $perm){
			//If user is in permission or in group's permission
			if(_core()->user()->isInGroup($perm['entity_id']) || $perm['entity_id'] == _core()->user()->get('node_id')){
				if($perm['b_inverse']){//If permissions ARE INVERSED
					//Checking individual permissions
					if($perm['b_read'])
						$auths['read'] = false;
					if($perm['b_add'])
						$auths['add'] = false;
					if($perm['b_edit'])
						$auths['edit'] = false;
					if($perm['b_delete'])
						$auths['delete'] = false;
					//Note: You can't cancel ownership and mastership
				}
			}
		}

		//For the parents, we check the mastership of the node, which is a "transmissive" property
		if(!$this->isRoot()){
			if($this->getParent()->getAuth('mastership')){
				$auths['mastership'] = true;
			}
		}

		//In any case, if the user has the mastership, he has all rights
		if($auths['mastership']){
			$auths['read'] = true;
			$auths['add'] = true;
			$auths['edit'] = true;
			$auths['delete'] = true;
		}

		return $auths;
	}

	/**
	 * This function test if the given entity is an owner of the node
	 * returns boolean
	 */
	public function isOwner($entity){
		if($this == $entity){
			return true;
		}
		foreach($this->getPermissions() as $perm){
			if($perm['entity_id'] == $entity->get('node_id') && $perm['b_ownership'] && !$perm['b_inverse']){
				return true;
			}
		}
		return false;
	}

	/**
	 * This function is used to get the owner(s) of this node
	 * return an array of node
	 */
	public function getOwners(){
		$owners = array();
		foreach($this->getPermissions() as $perm){
			if($perm['b_ownership'] && !$perm['b_inverse']){
				if($perm['entity_id'] == $this->properties['node_id']){//If the current node is self-owned (user node)
					$owners[] = $this;
				} else {
					$owners[] = _core()->node($perm['entity_id']);
				}
			}
		}
		return $owners;
	}

	/**
	 * Get first owner of the node
	 */
	public function getOwner(){
		$owners = $this->getOwners();
		if(count($owners) > 0){
			return $owners[0];
		}
		return false;
	}

	public final function getPermissions(){
		//Get all permissions for this node.
		if($this->perms == null){
			$query = 'SELECT * FROM node_permission WHERE node_id='.(int)$this->properties['node_id'];
			$this->perms = _core()->db()->getArray($query);
		}
		return $this->perms;
	}

	/**
	 * Recursive function to get the inherited node (for the permissions)
	 */
	public function getInheritedNode(){
		if($this->properties['inherit_permissions']){
			return $this->getParent()->getInheritedNode();
		}
		return $this;
	}

	public function __toString(){
		return $this->name;
	}

	/**
	 * This function returns the value of a property for this node.
	 */
	public function __get($key){
		$throw_error = true;
		if(!$this->auths['read'] && !in_array($key, $this->public)){//If user cannot read and key not public
			if($throw_error){
				$this->addReadError();
				return false;
			}
			//Theses values are specific to the case where you want to display something instead of an error
			if($key == 'title'){
				return _core()->localize('Forbidden');
			}
			if($key == 'type_name'){
				return 'secured';
			}
			return false;
		}

		if(array_key_exists($key, $this->properties)){//Test array key
			return $this->properties[$key];
		}

		//Adding all node_infos properties
		$query = 'SELECT * FROM node_info WHERE node_id='.$this->properties['node_id'];
		$this->properties = array_merge($this->properties, (array)_core()->db()->getRow($query));

		if(array_key_exists($key, $this->properties)){//Test array key
			return $this->properties[$key];
		}

		if($throw_error){
			_core()->error()->add(11, 2, 'Trying to get "'.$key.'"');
		}
		return false;
	}

	/**
	 * Return a string containing the URL to the node
	 * @param <string> $options
	 * @return <string> URL
	 */
	public function link($options = null){
		return _core()->link($this, $options);
	}

	/**
	 * Returns a formated string with the creation date.
	 */
	public final function getCreationDate($format = DATE_DEFAULT){
		$str = $this->get('creation');
		if($str == false){
			return false;
		}
		return _core()->date()->date($str, $format);
	}

	/**
	 * Returns a formated string with the modification date.
	 */
	public final function getModificationDate($format = DATE_DEFAULT){
		$str = $this->get('modification');
		if($str == false){
			return false;
		}
		return _core()->date()->date($str, $format);
	}

	/**
	 * Returns a boolean with the permission for the key (read, add, edit, delete)
	 */
	public final function getAuth($key){
		if(!array_key_exists($key, $this->auths)){//Test array key
			_core()->user()->addError(11);
			return false;
		}
		return (bool)$this->auths[$key];
	}

	/**
	 * Add a new node of this type
	 */
	public static function addNode(sys_config $config, node_generic $parent_node, $type_name){
		if(!$parent_node->getAuth('add')){
			$config->error()->add(13);
			return false;
		}

		$tmp = $parent_node->getAllowedChildTypes(); //Retrieve all allowed child types for the parent node
		if(!in_array($type_name, $tmp)){ //If this type of node is not permitted inside parent
			$config->error()->add(19);
			return false;
		}

		//From here we have everything we need to insert the new node inside the database:
		$config->db()->beginTransaction(); //Start transaction to enable rollback
		//Inserting basic informations on node
		$query = 'INSERT INTO node_generic (parent_node_id,type_name,creator,index_num) VALUES ('.$parent_node->get('node_id').',\''.$type_name.'\',\''.$config->secureString($config->user()).'\','.$parent_node->getNextChildIndex().')';
		if(!$config->db()->exec($query)){
			$config->error()->add(30, 3);
			$config->db()->rollbackTransaction();
			return false;
		}

		$id = $config->db()->getLastId();
		if($id == false){
			$config->error()->add(30, 3);
			$config->db()->rollbackTransaction();
			return false;
		}

		//If everything is good, continue with inserting advanced
		$query = 'INSERT INTO node_info (node_id,creation,modification) VALUES ('.$id.','.$config->date()->now().','.$config->date()->now().')';
		if(!$config->db()->exec($query)){
			$config->error()->add(30, 3);
			$config->db()->rollbackTransaction();
			return false;
		}
		if($config->user()->isConnected()){
			$query = 'INSERT INTO node_permission (entity_id,node_id,b_ownership) VALUES ('.$config->user()->get('node_id').','.$id.',1)';
			if(!$config->db()->exec($query)){
				$config->error()->add(30, 3);
				$config->db()->rollbackTransaction();
				return false;
			}
		}

		//Updating modification date for parent node
		$query = 'UPDATE node_info SET modification='.$config->date()->now().' WHERE node_id='.$parent_node->get('node_id');
		$config->db()->exec($query);
		$config->db()->commitTransaction();

		return (int)$id;
	}

	public static function get_add_form($action = '', $method = 'post'){
		require_once REAL_PATH.'includes/interface/class.ui_form.php';
		require_once REAL_PATH.'includes/interface/class.Input.php';
		require_once REAL_PATH.'includes/interface/class.ui_textarea.php';
		$form = new ui_form($action, $method);
		$form->setPrefix('add_');
		$form->add(new Input('title', 'Title :', 'text'));
		$form->add(new ui_textarea('content', 'Content :'));
		$form->add(new Input('tags', 'Tags :', 'text', ''));
		return $form;
	}

	/**
	 * Returns the next free child index for the current node.
	 */
	public final function getNextChildIndex(){
		$query = 'SELECT index_num FROM node_generic WHERE parent_node_id='.$this->properties['node_id'].' ORDER BY index_num DESC LIMIT 1';
		return (int)_core()->db()->getSingle($query) + 1;
	}

	/**
	 * Get the parent of the node
	 */
	public function getParent(){
		if($this->isRoot()){
			return $this;
		}
		return _core()->node($this->properties['parent_node_id']);
	}

	/**
	 * Return all parents in an array of node ordered from the nearest parent
	 * to the root node.
	 */
	public function getParents(){
		$result = array(); //Array of nodes
		if($this->properties['node_id'] != $this->properties['parent_node_id']){
			$tmp = $this;
			do {
				$tmp = $tmp->getParent();
				$result[] = $tmp;
			} while($tmp->get('node_id') != $tmp->get('parent_node_id'));
		}
		return $result;
	}

	/**
	 * Get the childs of the current node.
	 * You can specify options:
	 * [node property], asc|desc, (int) [limit], (Array) [allowed types]
	 */
	public function getChilds($order_by = 'index_num', $order = 'ASC', $limit = null, $types = array()){
		if(!$this->auths['read']){
			$this->addReadError();
			return false;
		}
		$result = array();
		$or_conditions = array();
		$and_conditions = array();
		//Check if the ordering type matches
		$column = array('type_name', 'title', 'index_num', 'creation', 'modification');
		if(!in_array($order_by, $column)){//If not a column name
			$order_by = 'index_num'; //Default column for ordering
			_core()->error()->add(0); //TODO: Needs to throw some kind of error
		}
		if($order != 'DESC'){//If not DESC
			$order = 'ASC'; //Then it must be ASC (default for SQL ordering)
		}
		foreach($types as $type){//For each type of node we want
			if(substr($type, 0, 1) == '-'){
				$type = substr($type, 1);
				if(_core()->isType($type)){//If the type exists
					$and_conditions[] = 'type_name!=\''.$type.'\''; //We add a condition
				}
			} elseif(_core()->isType($type)){//If the type exists
				$or_conditions[] = 'type_name=\''.$type.'\''; //We add a condition
			}
		}
		$query = 'SELECT n.node_id,'.$order_by.' FROM node_generic AS n, node_info AS i WHERE n.parent_node_id='.$this->properties['node_id'].' AND n.node_id!='.$this->properties['node_id'].' AND n.node_id=i.node_id ';
		if(count($or_conditions) > 0){
			$query.=' AND ('.implode(' OR ', $or_conditions).')';
		}
		if(count($and_conditions) > 0){
			$query.=' AND '.implode(' AND ', $and_conditions);
		}
		$query.=' ORDER BY '.$order_by.' '.$order;
		if($limit != null){
			$query.=' LIMIT '.(int)$limit;
		}
		$tmp = _core()->db()->getArray($query);
		foreach($tmp as $value){
			$result[] = _core()->node($value['node_id']);
		}
		return $result;
	}

	/**
	 * Return the localized name of the type of this node.
	 */
	public final function getType(){
		return _core()->getLocalizedType($this->get('type_name'));
	}

	/**
	 * Return an array with the allowed types for this node.
	 * This is probably not what you are looking for, check the next function.
	 * For futur use if we authorize node conversion
	 */
	public function getAllowedTypes(){
		if(!($this->auths['read'] || $this->auths['add'] || $this->auths['edit'])){
			$this->addReadError();
			return false;
		}
		$query = 'SELECT a.allowed_type FROM node_generic AS n, allowed_type AS a WHERE n.type_name=a.type_name AND n.node_id='.$this->properties['parent_node_id'];
		$tmp = _core()->db()->getArray($query);
		$tmp2 = array();
		foreach($tmp as $value){
			$tmp2[] = $value['allowed_type'];
		}
		return $tmp2;
	}

	/**
	 * Return an array with the authorized types for the childs inside this node.
	 */
	public function getAllowedChildTypes(){
		if(!($this->auths['read'] || $this->auths['add'] || $this->auths['edit'])){
			$this->addReadError();
			return false;
		}
		$query = 'SELECT a.allowed_type FROM node_generic AS n, allowed_type AS a WHERE n.type_name=a.type_name AND n.node_id='.$this->properties['node_id'];
		$tmp = _core()->db()->getArray($query);
		$tmp2 = array();
		foreach($tmp as $value){
			$tmp2[] = $value['allowed_type'];
		}
		return $tmp2;
	}

	/**
	 * TODO: Needs rewriting !
	 */
	public function delete($transaction = true){
		//Check permissions
		if(!$this->auths['delete']){
			$this->addDelError();
			return false;
		}

		//Some elements can't be deleted
		if($this->isRoot() || in_array($this->properties['type_name'], array('users', 'groups'))){
			_core()->error()->add(18);
			return false;
		}

		//This is a recursive call to delete all child nodes
		//If a node can't be deleted, then the process just stops.

		if($transaction){
			_core()->db()->beginTransaction();
		}

		$tmp = $this->getChildren(); //Get all childs
		if(count($tmp) > 0){//If there is any
			foreach($tmp as $child){//Try to delete each child
				if(!$child->delete(false)){//Just continue on success
					_core()->error()->error(22);
					if($transaction){
						_core()->db()->rollbackTransaction();
					}
					return false;
				}
			}
		}


		$tmp = $this->deleteMore($transaction);
		if(!$tmp){
			_core()->error()->add(0); //TODO: Throw some error
			if($transaction){
				_core()->db()->rollbackTransaction();
			}
			return false;
		}

		$query = 'DELETE FROM node_permission WHERE node_id='.$this->properties['node_id'];
		if(!_core()->db()->exec($query)){
			_core()->error()->add(0); //TODO: Throw some error
			if($transaction){
				_core()->db()->rollbackTransaction();
			}
			return false;
		}

		$query = 'DELETE FROM node_generic WHERE node_id='.$this->properties['node_id'];
		if(!_core()->db()->exec($query)){
			_core()->error()->add(0); //TODO: Throw some error
			if($transaction){
				_core()->db()->rollbackTransaction();
			}
			return false;
		}

		if($transaction){
			_core()->db()->commitTransaction();
		}

		//Updating modification date for parent
		$query = 'UPDATE node_info SET modification='._core()->date()->now().' WHERE node_id='.$this->properties['parent_node_id'];
		_core()->db()->exec($query);
		return true;
	}

	protected function deleteMore($transaction){
		$query = 'DELETE FROM node_info WHERE node_id='.$this->properties['node_id'];
		if(!_core()->db()->exec($query)){
			return false;
		}
		return true;
	}

	/**
	 * This functions accepts no arguments, in this case all forms related to the
	 * node will be edited, it also accepts a form (from the ui_form class) or an
	 * array of forms. This method saves all the data in the form inside the
	 * database.
	 */
	public function edit($forms = null){
		if(!$this->auths['edit']){//Test permissions
			$this->addEditError();
			return false;
		}

		if($forms == null){
			$forms = $this->form();
		}

		if(!is_array($forms)){
			$forms = array($forms);
		}

		foreach($forms as $form){
			if(!$form->isActive()){
				break;
			}
			if(!$form->validate()){
				_core()->error()->add(31);
				return false;
			}

			/**
			 * EDITING COMMON INFORMATIONS
			 */
			$list = array();
			if($form->get('title')){
				$list[] = 'title=\''._core()->secureString($form->getValue('title')).'\'';
			}
			if($form->get('index_num')){
				$list[] = 'index_num='.(int)$form->getValue('index_num');
			}
			foreach(array('read', 'add', 'edit', 'delete') as $p){
				if($form->get('anonymous.'.$p)){
					$list[] = 'anonymous_'.$p.'='.(int)(bool)$form->getValue('anonymous.'.$p);
				}
			}
			if(!$this->isRoot() && $form->get('inherit_permissions')){
				$list[] = 'inherit_permissions='.(int)(bool)$form->getValue('inherit_permissions');
			}
			if(count($list) > 0){
				$query = 'UPDATE node_generic SET '.implode(', ', $list).' WHERE node_id='.$this->properties['node_id'];
				if(!_core()->db()->exec($query)){
					_core()->error()->add(0); //TODO : Throw some error
					return false;
				}
			}

			//Editing node_info
			$list = array();
			if($form->get('content')){
				$list[] = 'content=\''._core()->secureString($form->getValue('content'), true).'\'';
			}
			if($form->get('tags')){
				$list[] = 'tags=\''._core()->secureString($form->getValue('tags')).'\'';
			}
			if($form->get('modification')){
				$list[] = 'modification='._core()->date()->now();
			}
			if(count($list) > 0){
				$query = 'UPDATE node_info SET '.implode(', ', $list).' WHERE node_id='.$this->properties['node_id'];
				if(!_core()->db()->exec($query)){
					_core()->error()->add(0);
					return false;
				}
			}

			/**
			 * EDITING NODE'S PERMISSIONS
			 */
			//Save all changes on acive permissions
			foreach($this->getPermissions() as $permission){
				$list = array();
				foreach(array('read', 'add', 'edit', 'delete', 'ownership', 'mastership', 'inverse') as $perm){
					if($form->get('entity_'.$permission['entity_id'].'.'.$perm)){
						$list[] = 'b_'.$perm.'='.(int)(bool)$form->getValue('entity_'.$permission['entity_id'].'.'.$perm);
					}
				}
				if(count($list) > 0){
					$query = 'UPDATE node_permission SET '.implode(', ', $list).' WHERE node_id='.$this->properties['node_id'].' AND entity_id='.$permission['entity_id'];
					if(!_core()->db()->exec($query)){
						_core()->error()->add(0); //TODO : Throw some error
						return false;
					}
				}
			}

			/**
			 * ARGH! Can't find another way to do this !!!
			 */
			if(isset($_POST['add_new_entity'])){
				$query = 'INSERT INTO node_permission (entity_id,node_id,b_read,b_add,b_edit,b_delete,b_ownership,b_mastership,b_inverse) VALUES (';
				$query.=(int)$form->getValue('new_entity_id').','.$this->properties['node_id'];
				foreach(array('read', 'add', 'edit', 'delete', 'ownership', 'mastership', 'inverse') as $perm){
					$query.=','.(int)$form->getValue('new.'.$perm);
				}
				$query.=')';
				if(!_core()->db()->exec($query)){
					_core()->error()->add(0); //TODO:Throw some error
				}
			}
		}

		return true;
	}

	public function removePermission($id){
		$query = 'DELETE FROM node_permission WHERE node_id='.$this->properties['node_id'].' AND entity_id='.(int)$id;
		if(!_core()->db()->exec($query)){
			_core()->error()->add(0); //TODO : Throw some error
			return false;
		}
		return true;
	}

	/**
	 * Initialize all basic fields for node edition
	 * where you can add your customs inputs.
	 */
	protected function initContentForm(){
		if(!$this->auths['edit']){
			//$this->addEditError();
			return false;
		}
		//Form initialization
		require_once REAL_PATH.'includes/interface/class.ui_form.php';
		$form = new ui_form();
		//Prefix used for all the inputs names to avoid duplicate ids
		$form->setPrefix('n['.$this->properties['node_id'].']['); //Don't change this !!!
		$form->setSuffix(']');
		$form->setName('node_'.$this->properties['node_id'].'_content'); //And this also.
		//
		//Basic HTML inputs for most of the fields
		require_once REAL_PATH.'includes/interface/class.Input.php';
		require_once REAL_PATH.'includes/interface/class.ui_textarea.php';

		/**
		 * The following lines are all the basics information a node should contain
		 */
		$form->add(new Input('title', _core()->localize('Title').' : ', 'text', $this->get('title'), true));
		$form->add(new ui_textarea('content', _core()->localize('Content').' : ', false, $this->get('content')));
		$form->add(new Input('tags', _core()->localize('Tags').' : ', 'text', $this->get('tags')));

		$this->form['content'] = $form;
		return $form;
	}

	/**
	 * Initialize all basic fields for node edition
	 * where you can add your customs inputs.
	 */
	protected function initPermissionsForm(){
		if(!$this->auths['edit']){
			//$this->addEditError();
			return false;
		}
		if(!($this->auths['ownership'] || $this->auths['mastership'])){//The user can't modify permissions if he doesn't have ownership or mastership
			//_core()->error()->add(0);//TODO !!!!
			return false;
		}

		//Form initialization
		require_once REAL_PATH.'includes/interface/class.ui_form.php';
		$form = new ui_form();
		//Prefix used for all the inputs names to avoid duplicate ids
		$form->setPrefix('n'.$this->properties['node_id'].'_'); //Don't change this !!!
		$form->setName('node_'.$this->properties['node_id'].'_permissions'); //And this also.
		//
		//Basic HTML inputs for most of the fields
		require_once REAL_PATH.'includes/interface/class.Input.php';
		require_once REAL_PATH.'includes/interface/class.Checkbox.php';
		require_once REAL_PATH.'includes/interface/class.Select.php';

		$form->add(new Checkbox('inherit_permissions', _core()->localize('Inherit permissions').' : ', (int)$this->get('inherit_permissions')));
		$form->add(new Checkbox('anonymous.read', null, (int)$this->get('anonymous_read')));
		$form->add(new Checkbox('anonymous.add', null, (int)$this->get('anonymous_add')));
		$form->add(new Checkbox('anonymous.edit', null, (int)$this->get('anonymous_edit')));
		$form->add(new Checkbox('anonymous.delete', null, (int)$this->get('anonymous_delete')));

		/*
		 * Already registered entities
		 */
		$active_entities = array();
		foreach($this->getPermissions() as $permission){
			$label = $permission['entity_id'].' - '._core()->node($permission['entity_id']);
			foreach(array('read', 'add', 'edit', 'delete', 'ownership', 'mastership', 'inverse') as $perm){
				$form->add(new Checkbox('entity_'.$permission['entity_id'].'.'.$perm, null, $permission['b_'.$perm]));
			}
			$active_entities[$permission['entity_id']] = $label;
		}

		/*
		 * Looking for other entities not yet in the permissions and propose to add them.
		 */
		$query = 'SELECT node_id FROM node_generic WHERE type_name=\'user\' OR type_name=\'group\'';
		$ids = _core()->db()->getArray($query);
		$inactive_entities = array();
		foreach($ids as $id){
			if(!array_key_exists($id['node_id'], $active_entities)){
				$inactive_entities[$id['node_id']] = $id['node_id'].' - '._core()->node($id['node_id']);
			}
		}
		if(count($inactive_entities) > 0){
			$form->add(new Select('new_entity_id', _core()->localize('New').' : ', $inactive_entities));
			foreach(array('read', 'add', 'edit', 'delete', 'ownership', 'mastership', 'inverse') as $perm){
				$form->add(new Checkbox('new.'.$perm, null));
			}
		}

		$this->form['permissions'] = $form;
		return $form;
	}

	/**
	 * Initialize all basic fields for node edition
	 * where you can add your customs inputs.
	 */
	protected final function initPositionForm(){
		if(!$this->auths['edit']){
			//$this->addEditError();
			return false;
		}

		//Form initialization
		require_once REAL_PATH.'includes/interface/class.ui_form.php';
		$form = new ui_form();
		//Prefix used for all the inputs names to avoid duplicate ids
		$form->setPrefix('n'.$this->properties['node_id'].'_'); //Don't change this !!!
		$form->setName('node_'.$this->properties['node_id'].'_position'); //And this also.

		require_once REAL_PATH.'includes/interface/class.Input.php';
		require_once REAL_PATH.'includes/interface/class.Select.php';

		//TODO for move and copy
		$possible_parents = array($this->get('parent_node_id', false) => $this->getParent());
		$form->add(new Select('parent_node_id', _core()->localize('Parent node').' : ', $possible_parents, $this->get('parent_node_id')));

		$this->form['position'] = $form;
		return $form;
	}

	/**
	 * Return the form object
	 */
	public function form($key = null){
		if($key === null){//If no key is passed, we return an array of forms
			$this->initContentForm();
			$this->initPermissionsForm();
			$this->initPositionForm();
			return $this->form;
		}

		if(isset($this->form[$key])){//If a key is specified, return the corresponding form
			return $this->form[$key];
		}

		$method = 'init'.$key.'Form';
		if(method_exists($this, $method)){//try to init the form if it doesn't already exists
			call_user_func(array($this, $method));
			if(isset($this->form[$key])){//If a key is specified, return the corresponding form
				return $this->form[$key];
			}
		}
		return false;
	}

	/**
	 * Test if the node is a root node
	 */
	public final function isRoot(){
		return $this->properties['node_id'] == $this->properties['parent_node_id'];
	}

	//READ/ADD/EDIT/DEL ERRORS
	//These function are meant to prevent the call of two time the same error message.
	protected final function addReadError(){
		if($this->single_error['read']){
			_core()->error()->add(12);
		}
		$this->single_error['read'] = false;
	}

	protected final function addAddError(){
		if($this->single_error['add']){
			_core()->error()->add(13);
		}
		$this->single_error['add'] = false;
	}

	protected final function addEditError(){
		if($this->single_error['edit']){
			_core()->error()->add(14);
		}
		$this->single_error['edit'] = false;
	}

	protected final function addDelError(){
		if($this->single_error['delete']){
			_core()->error()->add(15);
		}
		$this->single_error['delete'] = false;
	}

	//Everything beyond that line is used to generate content

	public function button($size = ICON_SMALL, $action = '', $attributes = array()){
		return _core()->generateButtonFromNode($this, $size, $action, $attributes);
	}

	public function actionButton($action, $title = null, $size = ICON_SMALL, $attributes = array()){
		return _core()->generateActionButtonFromNode($this, $action, $title, $size, $attributes);
	}

	/**
	 * Getting the thumbnail
	 */
	public function getThumb(){
		if($this->getAuth('read')){
			$icons = $this->getChildren('index_num', 'ASC', null, array('icon'));
			if(count($icons) > 0){//If there are icons
				foreach($icons as $icon){//We take the first one we are authorized to see
					if($icon->getAuth('read')){
						return $icon->getThumb(); //Return the file if found
					}
				}
			}
		}
		//Else return default icon for type of node
		return _core()->getThumb($this->get('type_name', false));
	}

	public function getHtmlThumb($size = ICON_SMALL){
		if($this->getAuth('read')){
			return _core()->generateThumbnail($this->getThumb(), $size, $this);
		}
		return _core()->generateThumbnail(_core()->getThumb('secured'), $size, $this);
	}

	public function getHtmlContent(){
		if(!$this->auths['read']){//Test permissions
			$this->addReadError();
			return false;
		}
		$string = '<h1>'.$this.'</h1>';
		$string.=$this->get('content');
		return $string;
	}

}
