<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Buoy extends BaseModel {
	public $buoyid;
	public $name;

  public $buoyReports; //array of Buoy Report objects
}
?>