<?
class Reporter {
	public $id;
	public $name;
	public $email;
	public $joindate;
	public $public;

	public function __construct($data) {
		// convert to array if $data is already an object
		if (is_object($data)) {
			$data = get_object_vars($data);
		}

		// copy all the values from $data to $this
		foreach($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}		
	}		
}
?>