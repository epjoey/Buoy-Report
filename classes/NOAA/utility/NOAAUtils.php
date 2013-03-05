<?
class NOAAUtils {
	static function isTideSet($tide) {
		return isset($tide) && !in_array(abs(intval($tide)), array(99, 999, 9999, 99999));
	}
}
?>