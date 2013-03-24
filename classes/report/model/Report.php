<?
class Report {
	public $id;
	public $text;
	public $locationid;
	public $reporterid;
	public $quality;
	public $imagepath;
	public $obsdate; //date of observation (surf session)
	public $reportdate; //date of report (when report was logged)
	public $public;
	public $waveheight;
	public $sublocationid;

	//from other tables
	public $buoyData; //array of BuoyData models
	public $tideData; //array of tideData models
	public $location; //Location Model Object
	public $sublocation; //Sublocation Model Object
	public $reporter; //Reporter Model Object

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