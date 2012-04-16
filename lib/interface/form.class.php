<?php

class Form{

  protected $inputs=array(); //List of inputs
  protected $order=array(); //Order of the inputs
  protected $name;
  protected $method;
  protected $action;
  protected $prefix='';
  protected $title;

  public function __construct($action='', $method='post'){
	$this->action=$action;
	$this->method=$method;
  }

  public function set_name($name){
	$this->name=$name;
  }

  public function get_name(){
	return $this->$name;
  }
  
  public function set_title($title){
	$this->title=$title;
  }

  public function get_title(){
	return $this->title;
  }

  public function form_tag($attributes=array()){
	$str='<form action="'.$this->action.'" method="'.$this->method.'" enctype="multipart/form-data" '.proto_board::get_attributes_from_array($attributes).'>';
	return $str;
  }

  public function add($newinput, $position=null){
	$newinput->set_prefix($this->prefix);
	$this->inputs[$newinput->get_name()]=$newinput;
	if($position !== null){
	  array_splice($this->order, $position, 0, $newinput->get_name());
	} else {
	  $this->order[]=$newinput->get_name();
	}
  }

  public function remove($name){
	if($name===(int)$name){
	  $num=$name;
	  if(!isset($this->order[$num])){
		return false;
	  }
	  unset($this->inputs[$this->order[$num]]);
	  array_splice($this->order, $num, 1);
	  return true;
	}
	if(!isset($this->inputs[$name])){
	  return false;
	}
	unset($this->inputs[$name]);
	array_splice($this->order, array_search($name, $this->order), 1);
	return true;
  }

  public function get($name){
	if($name===(int)$name){
	  $num=$name;
	  if(!isset($this->order[$num])){
		return false;
	  }
	  return $this->inputs[$this->order[$num]];
	}
	if(!isset($this->inputs[$name])){
	  return false;
	}
	return $this->inputs[$name];
  }

  public function get_action(){
	return $this->action;
  }

  public function set_action($action){
	$this->action=$action;
  }

  public function get_method(){
	return $this->method;
  }

  public function set_method($method){
	$this->method=$method;
  }

  public function get_value($name){
	if(!isset($this->inputs[$name])){
	  return false;
	}
	return $this->inputs[$name]->get_value();
  }

  public function get_all(){
	$tmp=array();
	foreach($this->order as $name){
	  $tmp[$name]=$this->inputs[$name];
	}
	return $tmp;
  }

  public function display($name){
	if(!isset($this->inputs[$name])){
	  echo '<div class="error">Unknown input: '.$name.'</div>';
	  return false;
	}
	echo $this->inputs[$name]->display();
  }

  public function display_all(){
	foreach($this->inputs as $input){
	  echo $input->display();
	}
  }

  public function set_prefix($value){
	$this->prefix=$value;
	foreach($this->inputs as $input){
	  $input->set_prefix($value);
	}
  }

  public function get_names(){
	return array_keys($this->inputs);
  }

  public function clear(){
	unset($this->inputs);
	$this->inputs=array();
  }

  public function validate(){
	foreach($this->inputs as $input){
	  if(!$input->validate()){
		return false;
	  }
	}
	return true;
  }

}
