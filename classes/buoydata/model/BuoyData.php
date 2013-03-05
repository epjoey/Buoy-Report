<?
class BuoyData {
	public $id;
	public $reportid; //which "report" it corresponds to
	public $buoy;
	public $winddir;
	public $windspeed;
	public $swellheight;
	public $swellperiod;
	public $swelldir;
	public $tide;
	public $gmttime;
	public $watertemp;

	//from other table
	public $buoyModel; //Buoy Model Object
	
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