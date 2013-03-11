<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/utility/BuoyDataViewUtils.php';


class BuoyDataView {
	static function renderBuoyData($buoyData, $timezone = null) {
		if ($timezone) {
			$buoyTime = getLocalTimeFromGMT($buoyData->gmttime, $timezone);
			$tzAbbrev = getTzAbbrev($timezone);
		} else {
			$buoyTime = gmstrftime("%m/%d/%Y %l:%M %p", $buoyData->gmttime);
			$tzAbbrev = "GMT";		
		}
		?>
		<div class="buoy-data-item">
			<span class="station-head">
				Buoy 
				<a target="_blank" href="<?=Path::toNOAABuoy($buoyData->buoy)?>"><?= $buoyData->buoy ?></a>
				(<?= $buoyData->buoyModel->name ?>)
			</span>
			<ul class="station-data-fields">
				<li><span class="label">Time of Report:</span> <?=$buoyTime?> (<?=$tzAbbrev?>)</li>
				<li><span class="label">Wind Direction:</span> <?= BuoyDataViewUtils::render($buoyData->winddir, 'wind-direction') ?></li>
				<li><span class="label">Wind Speed:</span> <?= BuoyDataViewUtils::render($buoyData->windspeed, 'wind-speed') ?></li>
				<li><span class="label">Swell Height:</span> <?= BuoyDataViewUtils::render($buoyData->swellheight, 'swell-height') ?></li>
				<li><span class="label">Swell Period:</span> <?= BuoyDataViewUtils::render($buoyData->swellperiod, 'swell-period') ?></li>
				<li><span class="label">Swell Direction:</span> <?= BuoyDataViewUtils::render($buoyData->swelldir, 'swell-direction') ?></li>
				<li><span class="label">Water Temp:</span> <?= BuoyDataViewUtils::render($buoyData->watertemp, 'water-temp') ?></li>
				<li><span class="label">Tide:</span> <?= BuoyDataViewUtils::render($buoyData->tide, 'tide') ?></li>
			</ul>
		</div>
		<?
	}	
}
?>