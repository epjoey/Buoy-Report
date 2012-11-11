<?
class ObjectUtility {
	static function pluck($objects = array(), $prop) {
		$props = array();
		foreach($objects as $obj) {
			$props[] = $obj[$prop];
		}	
		return $props;
	}
}

?>