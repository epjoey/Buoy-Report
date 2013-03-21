<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';
$buoys = BuoyService::getAllBuoys();
?>
<div class="buoy-selector">
	<?
	foreach($buoys as $buoy) {
		?>
		<div class="item" stationid="<?=$buoy->buoyid?>">
			<span class="id"><?= $buoy->buoyid ?></span>
			<span class="name"><?= $buoy->name ?></span>
		</div>
		<?
	}
	?>
</div>