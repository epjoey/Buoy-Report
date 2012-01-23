<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

$stationType = $_REQUEST['stationType'];
$locationId = $_REQUEST['locationid'];
$to = $_REQUEST['to'];
$limit = 1000;

if ($stationType == 'buoy') {
	$stationId = 'buoyid';
	$stationName = 'name';
	$table = 'buoy';
} else if ($stationType == 'tide') {
	$stationId = 'stationid';
	$stationName = 'stationname';
	$table = 'tidestation';	
} else {
	?>
	<span class="error">Station type is not specified</span>
	<?
}

$stations = Persistence::getAllStations($table, $limit);

?>
<h4>Choose a <?=$stationType?></h4>
<ul>
	<?
	foreach($stations as $station) {
		if (!isset($station[$stationName]) || $station[$stationName] == "") {
			$station[$stationName] = 'No Label';
		}
		
		$url = Paths::$to($locationId) . "&submit=existing".$stationType."&".$stationType."=" . $station[$stationId];

		?>
		<a href="<?=$url?>" class="station" station-id="<?=$station[$stationId]?>" station-name = "<?=$station[$stationName]?>">
			<span class="station-id"><?= $station[$stationId] ?></span>
			<span class="station-name"><?= $station[$stationName] ?></span>
		</a>
		<?
	}
	?>
</ul>
<script>	
	$('#existing-buoys-container .station').click(function(){
		var stationId = $(this).attr('station-id');
		var stationName = $(this).attr('station-name');
		console.log(stationId); console.log(stationName);								
	});
</script>
