<?
class Reporter extends BaseModel {
	public $id;
	public $name;
	public $email;
	public $joindate;
	public $public;	

	public $locations = null; //array of Location models
}
?>