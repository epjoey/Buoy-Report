<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ModelCache {

	static $CACHE;

	static function get($modelName, $ids) {
		$models = array();
		foreach($ids as $id) {
			if (isset(self::$CACHE[$modelName][$id])) {
				//error_log("$modelName $id used cache");
				$models[$id] = self::$CACHE[$modelName][$id];
			}
		}
		return $models;
	}

	static function set($modelName, $id, $model) {
		self::$CACHE[$modelName][$id] = $model;
	}
}
?>