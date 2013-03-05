<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/reporter/model/Reporter.php';

/*
 *	takes array of reporter ids and returns those reporters
 */
class ReporterPersistence {
	public static function getReporters($ids, $options = array()) {
		if (!$ids) {
			return array();
		}
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'id DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE id in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM reporter $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$reporters = array();
		while ($row = mysqli_fetch_object($result)) {	
			$reporters[] = new Reporter($row);
		}
		return $reporters;
	}
}
?>