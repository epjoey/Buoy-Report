<?
class TideData {
	public $id;
	public $tide;
	public $tidedate;
	public $tideRise; // 1 or -1
	public $predictedTide; // 1 or -1
	public $reportid; //which "report" it corresponds to
	public $tidestation; //stationid

	//from other table
	public $tideStationModel; //TideStation Model Object
	
	
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