<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class SingleReport {


	public static function renderSingleReport($report, $options = array()) {
		$defaultOptions = array(
			'expanded' => true
		);
		$options = array_merge($defaultOptions, $options);

		//pass this in
		if (isset($report->location->timezone)) {
			$obsTime = getLocalTimeFromGMT($report->obsdate, $report->location->timezone);
			$tzAbbrev = getTzAbbrev($report->location->timezone);
		} else {
			$obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $report->obsdate);
			$tzAbbrev = "GMT";						
		}
		?>

		<li class="report expanded" reportid="<?= $report->id ?>" reporterid="<?=$report->reporterid?>">
			<ul>
				<li class="report-head">
					<a class="loc-name" href="<?=Path::toLocation($report->locationid);?>"><?= html($report->location->locname)?></a>
					<?
					if (isset($report->sublocation)) {
						?>
						<span class="tz"><?= html($report->sublocation->sl_name) ?></span>
						<?
					}
					?>
					<span class="obs-time"><?=$obsTime?> <span class="tz"><?=$tzAbbrev?></span></span>
				</li>
				<? 
				if (isset($report->quality)) {
					$qualityOptions = ReportUtils::getQualityOptions();
					$qualityText = $qualityOptions[$report->quality];
					?>
					<li class="quality rating<?= $report->quality ?>">
						<ul class="ratings">
							<li title="<?= $qualityOptions[1]; ?>" class="level one"></li>
							<li title="<?= $qualityOptions[2]; ?>" class="level two"></li>
							<li title="<?= $qualityOptions[3]; ?>" class="level three"></li>
							<li title="<?= $qualityOptions[4]; ?>" class="level four"></li>
							<li title="<?= $qualityOptions[5]; ?>" class="level five"></li>
						</ul>
						<span class="text"><?= html($qualityText) ?> session</span>
					</li>
					<?
				}

				//render a thumbnail image on page load.
				if(isset($report->imagepath)) { 
					Image::render($report->imagepath, $thumb = TRUE);
				}
				
				if($report->waveheight) { 
					$heightOptions = ReportUtils::getWaveHeightsOptions();
					$height = $report->waveheight;
					?>
					<li class="waveheight"><?= $heightOptions[$height][0] . '-' . $heightOptions[$height][1] . '&rsquo;' ?></li>
					<? 
				} 	

				if($report->text) { 
					?>
					<li class="text-report"><?= $report->text ?></li>
					<? 
				} 				

				//loading buoy/tide details. setting up li's for ajax inserting. js will check if elems exist 
				?>	
				<li class="detail-section">
					<?
					/* rendered if on single page or new-report ajax */
					
					Image::render($report->imagepath, false);
					self::renderBuoyReports($report);
					self::renderTideReports($report);
					self::renderReporter($report);
					?>
				</li>
			</ul>
			<span class="notification-icons">
				<? if ($report->buoyReports) { ?>
					<span class="buoy-icon icon" title="<?=$report->location->locname?> has buoy stations">B</span>
				<? } ?>
				<? if ($report->tideReports) { ?>
					<span class="tide-icon icon" title="<?=$report->location->locname?> has a tide station">T</span>
				<? } ?>		
			</span>	
			<div class="click-to-expand">
				<span>Click To</span>
				<div>Expand</div>
			</div>
		</li>
		<?		
	}	

	public static function renderBuoyReports($report) {
		if (!$report->buoyReports) {
			return;
		}
		?>
		<ul class="buoy-data">
			<? 
			foreach ($report->buoyReports as $buoyReport) { 
				?>
				<li>
					<?
					BuoyReportView::renderBuoyReport($buoyReport, $report->location->timezone);
					?>
				</li>
				<? 
			}
			?>
		</ul>
		<? 
	}

	public function renderTideReports($report) {
		if (!$report->tideReports) {
			return;
		}		
		?>
		<ul class="buoy-data">
			<? 
			foreach ($report->tideReports as $tideReport) { 
				?>
				<li>
					<?
					TideReportView::renderTideReport($tideReport, $report->location->timezone);
					?>
				</li>
				<? 
			}
			?>
		</ul>		
		<?	
	}

	public function renderReporter($report) {
		if (isset($report->location->timezone)) {
			$reportTime = getLocalTimeFromGMT($report->reportdate, $report->location->timezone);
			$tzAbbrev = getTzAbbrev($report->location->timezone);
		} else {
			$reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report->reportdate);
			$tzAbbrev = "GMT";
		}				
		?>
		<div class="reporter-details">
			<a href="<?=Path::toSingleReport($report->id)?>">report #<?=$report->id?></a> by <a href="<?=Path::toProfile($report->reporterid);?>"><?= html($report->reporter->name); ?></a> on <?= $reportTime ?> <span class="tz"><?=$tzAbbrev?></span>
		</div>
		<?
	}

	// public static function renderComments($comments) {
		
	// 	foreach ($comments as $comment) {
	// 		print $comment;
	// 	}
	// }


}

?>