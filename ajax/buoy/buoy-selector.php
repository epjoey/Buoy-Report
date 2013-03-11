<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';
$buoys = BuoyService::getAllBuoys();
?>
<div class="buoy-selector">
	<h4>Choose a Buoy</h4>
	<span class="status"></span>
	<?
	foreach($buoys as $buoy) {
		?>
		<div class="item" buoyid="<?=$buoy->buoyid?>">
			<?= $buoy->buoyid ?>
			<span class="name"><?=$buoy->name?></span>
		</div>
		<?
	}
	?>
</div>