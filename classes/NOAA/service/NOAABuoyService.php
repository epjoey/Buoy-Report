<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/persistence/NOAABuoyPersistence.php';
class NOAABuoyService {
	static function getBuoyDataFromBuoyAtTime($buoyId, $time) {
		if (!$buoyId || !$time) {
			throw new InternalException();
		}		
		return NOAABuoyPersistence::getBuoyDataFromBuoyAtTime($buoyId, $time);	
	}
}
?>