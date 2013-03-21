<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';
$stations = TideStationService::getAllTideStations();
?>
<div class="station-selector">
	<?
	foreach($stations as $station) {
		?>
		<div class="item" stationid="<?=$station->stationid?>">
			<span class="id"><?= $station->stationid ?></span>
			<span class="name"><?= $station->stationname ?></span>
		</div>
		<?
	}
	?>
</div>