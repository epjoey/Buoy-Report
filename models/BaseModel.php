<? 
class BaseModel {

	//return false if any properties in props are either falsy or not set on $this
	function has($props) {
		$props = explode('and', $props);
		foreach ($props as $prop) {
			if (!$this->$prop) {
				return false;
			}
			return true;
		}
	}
}
?>