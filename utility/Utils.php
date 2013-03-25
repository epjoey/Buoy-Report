<?
class Utils {
	static function pluck($objects = array(), $prop) {
		$props = array();
		foreach($objects as $obj) {
			if (!is_object($obj)) {
				$obj = (object)$obj;
			}
			$props[] = $obj->$prop;
		}	
		return $props;
	}

	static function compact($list) {
		foreach($list as $key => $val) {
			if ($val == null) {
				unset($list[$key]);
			}
		}
		return $list;
	}
}

?>