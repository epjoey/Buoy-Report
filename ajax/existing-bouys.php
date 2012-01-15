<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';

$stationType = $_REQUEST['stationType'];
$locationId = $_REQUEST['locationid'];

$stations = Persistence::getAllStations($stationType);

if ($stationType == 'bouy') {
	$stationId = 'bouyid';
	$stationName = 'name';
} else if ($stationType == 'tidestation') {
	$stationId = 'stationid';
	$stationName = 'stationname';	
} else {
	?>
	<span class="error">Station type is not specified</span>
	<?
}

?>
<h4>Choose a <?=$stationType?></h4>
<ul>
	<?
	foreach($stations as $station) {
		if (!isset($station[$stationName]) || $station[$stationName] == "") {
			$station[$stationName] = 'No Label';
		}
		?>
		<a href="<?=Paths::toLocation($locationId);?>&submit=existingbouy&bouy=<?=$station[$stationId]?>" class="station" station-id="<?=$station[$stationId]?>" station-name = "<?=$station[$stationName]?>">
			<span class="station-id"><?= $station[$stationId] ?></span>
			<span class="station-name"><?= $station[$stationName] ?></span>
		</a>
		<?
	}
	?>
</ul>
<script>	
	$('#existing-bouys-container .station').click(function(){
		var stationId = $(this).attr('station-id');
		var stationName = $(this).attr('station-name');
		console.log(stationId); console.log(stationName);								
	});
</script>
