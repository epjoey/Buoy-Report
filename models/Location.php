<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Location extends BaseModel {
	public $id;
	public $locname;
	public $timezone;
	public $creator;
	public $coverImagePath;
	
	//from other tables
	public $tideStations;
	public $buoys;
	public $sublocations;	
}
?>