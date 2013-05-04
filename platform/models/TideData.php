<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideData extends BaseModel {
	public $id;
	public $tide;
	public $tidedate;
	public $tideRise; // 1 or -1
	public $predictedTide; // 1 or -1
	public $reportid; //which "report" it corresponds to
	public $tidestation; //stationid

	//from other table
	public $tideStationModel; //TideStation Model Object
	

}
?>