<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';

class Location {

	public $id = 0;
	public $name = NULL;
	public $timezone = NULL;


	public function __construct($id){}

	public function loadData(){}

	public function insertLocation(){}

	public function getLocationCreator(){}

}
?>