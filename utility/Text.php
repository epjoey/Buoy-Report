<?
class Text {
	static function getMilesPerHourFromMetersPerSecond($metersPerSecond) {
		return round($metersPerSecond * 2.237, 1);
	}
	static function getFeetFromMeters($meters) {
		return round($meters * 3.28, 1);	
	}
}
?>