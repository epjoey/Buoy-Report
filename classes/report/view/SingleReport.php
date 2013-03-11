<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Text.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Mobile_Detect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/utility/ReportUtils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/view/BuoyDataView.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidedata/view/tideDataView.php';



class SingleReport {


	public static function renderSingleReport($report, $options = array()) {
		//var_dump($report);
		$defaultOptions = array(
			'showDetails' => false
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

		<li class="report <?= $options['showDetails'] ? 'expanded' : 'collapsed' ?>" reportid="<?= $report->id ?>" reporterid="<?=$report->reporterid?>">
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
					self::renderReportImage($report->imagepath, $thumb = TRUE);
				}
								
				if(isset($report->waveheight)) { 
					$heightOptions = ReportUtils::getWaveHeightsOptions();
					$height = $report->waveheight;
					?>
					<li class="waveheight"><?= $heightOptions[$height][0] . '-' . $heightOptions[$height][1] . '&rsquo;' ?></li>
					<? 
				} 	

				if(isset($report->text)) { 
					?>
					<li class="text-report"><?= bbcode2html($report->text) ?></li>
					<? 
				} 				

				//loading buoy/tide details. setting up li's for ajax inserting. js will check if elems exist 
				?>	
				<li class="detail-section">
					<?
					/* rendered if on single page or new-report ajax */
					if ($options['showDetails']) { 
						self::renderReportDetails($report, $options);
					}
					?>
				</li>
			</ul>
			<span class="notification-icons">
				<? if ($report->buoyData) { ?>
					<span class="buoy-icon icon" title="<?=$report->location->locname?> has buoy stations">B</span>
				<? } ?>
				<? if ($report->tideData) { ?>
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

	static function renderReportDetails($report) {
		self::renderReportImage($report);
		self::renderBuoyDetails($report);
		self::renderTideDetails($report);
		self::renderReporterDetails($report);
	}

	public static function renderReportImage($report, $thumbnail=FALSE) {
		$imagepath = $report->imagepath;
		if (!$imagepath) {
			return;
		}
		$detect = new Mobile_Detect();
		if ($thumbnail) {
			$detect->isSmallDevice() ? $dims = array(50,50) : $dims = array(80,80);	
		}
		else if (!$thumbnail) {
			$detect->isSmallDevice() ? $dims = array(280,260) : $dims = array(508,400);	
		}
		$image = getImageInfo($imagepath, $dims[0], $dims[1]);
		if (!empty($image)) {
			
			if ($thumbnail) {
				?>
				<li class="image-container thumbnail-image loading">
					<image realUrl="<?= $image['src'] ?>" src="" width="<?=$image['width']?>" height="<?=$image['height']?>"/>
				</li>
				<?
			} else {
				?>
				<div class="image-container large-image">
					<a href="<?=$image['src']?>" target="_blank">
						<image src="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/>
					</a>
				</div>
				<?	
			}				
		}			
	}

	public static function renderBuoyDetails($report) {
		if (!$report->buoyData) {
			return;
		}
		?>
		<ul class="buoy-data">
			<? 
			foreach ($report->buoyData as $buoyData) { 
				?>
				<li>
					<?
					BuoyDataView::renderBuoyData($buoyData, $report->location->timezone);
					?>
				</li>
				<? 
			}
			?>
		</ul>
		<? 
	}

	public function renderTideDetails($report) {
		if (!$report->tideData) {
			return;
		}		
		?>
		<ul class="buoy-data">
			<? 
			foreach ($report->tideData as $tideData) { 
				?>
				<li>
					<?
					TideDataView::renderTideData($tideData, $report->location->timezone);
					?>
				</li>
				<? 
			}
			?>
		</ul>		
		<?	
	}

	public function renderReporterDetails($report) {
		if (isset($report->location->timezone)) {
			$reportTime = getLocalTimeFromGMT($report->reportdate, $report->location->timezone);
			$tzAbbrev = getTzAbbrev($report->location->timezone);
		} else {
			$reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report->reportdate);
			$tzAbbrev = "GMT";
		}				
		?>
		<div class="reporter-details">
			<a href="<?=Path::toSingleReport($report->id)?>">report #<?=$report->id?></a> by <a href="<?=Path::toProfile($report->reporterid);?>"><?= html($report->reporter->name); ?></a> on <?= $reportTime ?> <span class="tz">(<?=$tzAbbrev?>)</span>
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