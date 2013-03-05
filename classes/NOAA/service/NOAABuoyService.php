<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/persistence/NOAABuoyPersistence.php';
class NOAABuoyService {
	static function getBuoyDataFromBuoyAtTime($buoyId, $time) {
		return NOAABuoyPersistence::getBuoyDataFromBuoyAtTime($buoyId, $time);	
	}
}
?>