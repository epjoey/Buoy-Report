<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyReport extends BaseModel  {
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

	// row number in the NOAA doc. For pagination.
	public $index;

	//from other table
	public $buoyModel; //Buoy Model Object	
}
?>