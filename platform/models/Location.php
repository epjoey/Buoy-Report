<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Location extends BaseModel {
	public $id;
	public $locname;
	public $timezone;
	public $creator;
	public $coverImagePath;
	
	//from other tables
	public $parentLocationId;
	public $parentLocation;
	public $tideStations;
	public $buoys;
	public $sublocations;	

	//derived in code
	public $urlName;

	function __construct($data) {
		parent::__construct($data);
		$this->urlName = urlencode($this->locname);
	}

}
?>