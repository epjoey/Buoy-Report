<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$data = $_POST['data'];
$reportId = $_POST['reportid'];

if (!$data && !$reportId) {
	printForm("data($data) or report id($reportId) are invalid");
	exit();
}


print();


function printForm($error = "") {
	print $error;
	?>
	<form type="post">
		<label>Report Id</label>
		<input type="text" name="reportid" />
		<br>
		<label>Buoy Data Row</label>
		<input type="text" name="data" />
	</form>
	<?
}
?>