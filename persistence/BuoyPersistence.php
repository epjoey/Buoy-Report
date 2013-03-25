<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyPersistence extends BasePersistence {

	static function getBuoys($ids) {
		$ids = Utils::compact($ids);
		$ids = array_map('Persistence::escape', $ids);
		if (!$ids) {
			return array();
		}
		$buoys = parent::getModelsFromCache('Buoy', $ids);
		$uncachedIds = array_diff($ids, array_keys($buoys));
		if (!$uncachedIds) {
			return $buoys;
		}
		$idStr = implode(',', $uncachedIds);
		$sql = "SELECT * FROM buoy WHERE buoyid in ($idStr)";
		$result = Persistence::run($sql);		
		while ($row = mysqli_fetch_object($result)) {	
			$buoy = new Buoy($row);
			$buoys[$buoy->buoyid] = $buoy;
			error_log("Buoy " . $buoy->buoyid . " used db");
			parent::cacheModel('Buoy', $buoy->buoyid, $buoy);
		}
		return $buoys;	
	}

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

	static function getBuoyIdsForLocation($location) {
		$id = intval($location->id);
		if (!$id) {
			return array();
		}
		$sql = "SELECT buoyid 
				FROM buoy_location
				WHERE locationid = '$id'";
		return Persistence::getArray($sql);		
	}	

	static function addBuoy($id, $name) {
		if (!$id || !$name) {
			throw new PersistenceException('addBuoy needs $id, $name');
		}		
		$id = Persistence::escape($id);
		$name = Persistence::escape($name);
		$sql = "INSERT INTO buoy SET buoyid = '$id', name = '$name'";
		Persistence::run($sql);
		return true;
	}

	static function updateBuoy($id, $name) {
		if (!$id || !$name) {
			throw new PersistenceException('updateBuoy needs $id, $name');
		}
		$id = Persistence::escape($id);
		$name = Persistence::escape($name);
		$sql = "UPDATE buoy SET name = '$name' WHERE buoyid = '$id'";
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