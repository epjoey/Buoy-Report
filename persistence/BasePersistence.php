<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BasePersistence {

	static $MODEL_CACHE;

	static function getModelsFromCache($modelName, $ids) {
		//var_dump(self::$MODEL_CACHE);
		$models = array();
		foreach($ids as $id) {
			if (isset(self::$MODEL_CACHE[$modelName][$id])) {
				error_log("$modelName $id used cache");
				$models[$id] = self::$MODEL_CACHE[$modelName][$id];
			}
		}
		return $models;
	}

	static function cacheModel($modelName, $id, $model) {
		self::$MODEL_CACHE[$modelName][$id] = $model;
	}
}
?>