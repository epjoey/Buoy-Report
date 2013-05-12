<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideReportView {
	static function renderTideReport($tideReport, $timezone = null) {
		if ($timezone) {
			$stationTime = getLocalTimeFromGMT($tideReport->tidedate, $timezone);
			$tzAbbrev = getTzAbbrev($timezone);
		} else {
			$stationTime = gmstrftime("%m/%d/%Y %l:%M %p", $tideReport->tidedate);
			$tzAbbrev = "GMT";		
		}
		?>

		<div class="buoy-data-item">
			<span class="station-head">
				Tide Station 
				<a target="_blank" href="<?=Path::toNOAATideStation($tideReport->tidestation)?>"><?= $tideReport->tidestation ?></a>
				(<?= $tideReport->tideStationModel->stationname ?>)
			</span>
			<ul class="station-data-fields">
				<li><span class="label">Time of Report:</span> <?=$stationTime?> (<?=$tzAbbrev?>)</li>
				<li><span class="label">Tide:</span> <?=TideReportViewUtils::render($tideReport, 'tide')?></li>
				<li><span class="label">Tide Rise:</span> 
					<?
					switch ($tideReport->tideRise) {
						case 1: print 'Incoming'; break;
						case -1: print 'Outgoing'; break;
						default: print 'none';
					}
					?>
				</li>
			</ul>
		</div>
		<?
	}	
}
?>