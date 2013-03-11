<?
class Location {
	public $id;
	public $locname;
	public $timezone;
	public $creator;

	//deprecated - do not use
	public $tidestation;
	public $buoy1;
	public $buoy2;
	public $buoy3;
	
	//from other tables
	public $tideStations;
	public $buoys;
	public $sublocations;

	public function __construct($data) {
		// convert to array if $data is already an object
		if (is_object($data)) {
			$data = get_object_vars($data);
		}

		// copy all the values from $data to $this
		foreach($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}		
	}		
}
?>