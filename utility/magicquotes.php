<?
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value) {
		$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes ($value) ; 
		
		return $value;
	}
	if (isset($_POST)) $_POST = array_map('stripslashes_deep', $_POST);
	if (isset($_GET)) $_GET = array_map('stripslashes_deep', $_GET);
	if (isset($_COOKIE)) $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	if (isset($_REQUEST)) $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
} 
?>