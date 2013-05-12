<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
var_dump($_POST);
$data = $_POST['data'];
$reportId = $_POST['reportid'];
$buoyId = $_POST['buoyid'];


if (!$data) {
	var_dump(getData());
	printForm($reportId, $buoyId, null, "enter data");
	exit();
}

if (!$reportId) {
	var_dump(getData($data));
	printForm(null, $buoyId, $data, "Enter Data with reportId");
	exit();

}

if (!$buoyId) {
	var_dump(getData($data));
	printForm($reportId, null, $data, "Enter Data with buoyId");
	exit();

}

$d = getData($data, $reportId, $buoyId);
var_dump($d);
print "entering data <br>";
BuoyReportService::saveBuoyReportsForReport(array($d));
print "entered data";
printForm($reportId, $buoyId, $data);


function getData($data = null, $reportId, $buoyId) {
	$row = array();
	if ($data) {
		$row = NOAABuoyPersistence::parseRowIntoData($data);
		$row['date'] = NOAABuoyPersistence::getTimestampOfRow($row);
	}
	return new BuoyReport(array(
		'reportid' => $reportId,
		'buoy' => $buoyId,
		'gmttime' => $row['date'],
		'swellheight' => $row[8],
		'swellperiod' => $row[9],
		'swelldir' => $row[11],
		'tide' => $row[18],
		'winddir' => $row[5],
		'windspeed' => $row[6],
		'watertemp' => $row[14]
	));
}

function printForm($reportId = null, $buoyId = null, $data = null, $error = "") {
	print $error;
	?>
	<h3>Remember that NOAA dates are in GMT (7 hours ahead of PDT)</h3>
	<form action="" method="post">
		<label>Report Id</label>
		<input type="text" name="reportid" value="<?= $reportId ?>" />
		<br>
		<label>Buoy Id</label>
		<input type="text" name="buoyid" value="<?= $buoyId ?>" />		
		<br>
		<label>Buoy Data Row</label>
		<input type="text" name="data" value="<?= $data ?>" style="width:500px"/>
		<br>
		<input type="submit" value="Submit">
	</form>
	<?
}
?>