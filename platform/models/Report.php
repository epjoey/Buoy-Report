<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Report extends BaseModel {
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
	public $buoyReports; //array of BuoyReport models
	public $tideReports; //array of tideReport models
	public $location; //Location Model Object
	public $sublocation; //Sublocation Model Object
	public $reporter; //Reporter Model Object	
}
?>