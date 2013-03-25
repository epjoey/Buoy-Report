<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideDataViewUtils {
	static function render($data, $type) {
		if (!isset($data)) { //todo:move to NOAAUtils::isSetBuoyData
			return '--';
		}
		switch ($type) {
		case 'tide':
			if (NOAAUtils::isTideSet($data->tide)) {
				return $data->tide . ' ft';	
			} elseif(NOAAUtils::isTideSet($data->predictedTide)) {
				return $data->predictedTide . ' ft (predicted)';	
			} else {
				return '--';
			}
			break;
		}
	}
}
?>