<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyReportView {
	static function renderBuoyReport($buoyReport, $timezone = null) {
		if ($timezone) {
			$buoyTime = getLocalTimeFromGMT($buoyReport->gmttime, $timezone);
			$tzAbbrev = getTzAbbrev($timezone);
		} else {
			$buoyTime = gmstrftime("%m/%d/%Y %l:%M %p", $buoyReport->gmttime);
			$tzAbbrev = "GMT";		
		}
		?>
		<div class="buoy-data-item">
			<span class="station-head">
				Buoy 
				<a target="_blank" href="<?=Path::toNOAABuoy($buoyReport->buoy)?>"><?= $buoyReport->buoy ?></a>
				(<?= $buoyReport->buoyModel->name ?>)
			</span>
			<ul class="station-data-fields">
				<li><span class="label">Time of Report:</span> <?=$buoyTime?> (<?=$tzAbbrev?>)</li>
				<li><span class="label">Wind Direction:</span> <?= BuoyReportViewUtils::render($buoyReport->winddir, 'wind-direction') ?></li>
				<li><span class="label">Wind Speed:</span> <?= BuoyReportViewUtils::render($buoyReport->windspeed, 'wind-speed') ?></li>
				<li><span class="label">Swell Height:</span> <?= BuoyReportViewUtils::render($buoyReport->swellheight, 'swell-height') ?></li>
				<li><span class="label">Swell Period:</span> <?= BuoyReportViewUtils::render($buoyReport->swellperiod, 'swell-period') ?></li>
				<li><span class="label">Swell Direction:</span> <?= BuoyReportViewUtils::render($buoyReport->swelldir, 'swell-direction') ?></li>
				<li><span class="label">Water Temp:</span> <?= BuoyReportViewUtils::render($buoyReport->watertemp, 'water-temp') ?></li>
				<li><span class="label">Tide:</span> <?= BuoyReportViewUtils::render($buoyReport->tide, 'tide') ?></li>
			</ul>
		</div>
		<?
	}	
}
?>