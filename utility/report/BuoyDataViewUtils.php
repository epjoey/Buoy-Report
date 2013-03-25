<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyDataViewUtils {
	static function render($data, $type) {
		if (!isset($data) || strtolower($data) == 'mm') { //todo:move to NOAAUtils::isSetBuoyData
			return '--';
		}
		switch ($type) {
		case 'wind-direction':
			return $data . '&deg (' . getDirection($data) . ')';
			break;
		case 'wind-speed':
			return Text::getMilesPerHourFromMetersPerSecond($data) . ' mph';
			break;
		case 'swell-height':
			return Text::getFeetFromMeters($data) . ' ft';
			break;
		case 'swell-period':
			return $data . ' sec';
			break;
		case 'swell-direction':
			return $data . '&deg (' . getDirection($data) . ')';
			break;
		case 'tide':
			return $data . ' ft';
			break;
		case 'water-temp':
			return $data . ' &deg' . 'C';
			break;
		}
	}
}
?>