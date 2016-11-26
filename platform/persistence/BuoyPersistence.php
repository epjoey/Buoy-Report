<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyPersistence {

	static function getAllBuoyIds($options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 1000,
			'order' => 'buoyid ASC'
		);
		$options = array_merge($defaultOptions, $options);		
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT buoyid FROM buoy ORDER BY $order LIMIT $start,$limit";
		return Persistence::getArray($sql);
	}

	static function insertBuoy($buoy) {
		if (!$buoy->buoyid || !$buoy->name) {
			throw new PersistenceException('addBuoy needs $id, $name');
		}		
		$id = Persistence::escape($buoy->buoyid);
		$name = Persistence::escape($buoy->name);
		$sql = "INSERT INTO buoy SET buoyid = '$id', name = '$name'";
		Persistence::run($sql);
		return true;
	}

	static function deleteBuoy($id) {
		if (!$id) {
			throw new PersistenceException('no id');
		}
		$id = Persistence::escape($id);
		$sql = "DELETE FROM buoy WHERE buoyid = '$id'";
		Persistence::run("DELETE FROM buoy WHERE buoyid = '$id'");
		Persistence::run("DELETE FROM buoy_location WHERE buoyid = '$id'");
		return true;				
	}	
}
?>