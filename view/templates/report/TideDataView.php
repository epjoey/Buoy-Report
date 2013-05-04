<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideDataView {
	static function renderTideData($tideData, $timezone = null) {
		if ($timezone) {
			$stationTime = getLocalTimeFromGMT($tideData->tidedate, $timezone);
			$tzAbbrev = getTzAbbrev($timezone);
		} else {
			$stationTime = gmstrftime("%m/%d/%Y %l:%M %p", $tideData->tidedate);
			$tzAbbrev = "GMT";		
		}
		?>

		<div class="buoy-data-item">
			<span class="station-head">
				Tide Station 
				<a target="_blank" href="<?=Path::toNOAATideStation($tideData->tidestation)?>"><?= $tideData->tidestation ?></a>
				(<?= $tideData->tideStationModel->stationname ?>)
			</span>
			<ul class="station-data-fields">
				<li><span class="label">Time of Report:</span> <?=$stationTime?> (<?=$tzAbbrev?>)</li>
				<li><span class="label">Tide:</span> <?=TideDataViewUtils::render($tideData, 'tide')?></li>
				<li><span class="label">Tide Rise:</span> 
					<?
					switch ($tideData->tideRise) {
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