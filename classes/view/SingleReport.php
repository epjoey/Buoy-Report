<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Mobile_Detect.php';



class SingleReport {


	public static function renderSingleReport($report, $options = array()) {

		$defaultOptions = array(
			'showDetails' => false,
			'locationHasBuoys' => false
		);
		$options = array_merge($defaultOptions, $options);

		//pass this in
		if (isset($report['buoy1']) || isset($report['buoy2']) || isset($report['buoy3'])) {
			$options['locationHasBuoys'] = true;
		}
		
		if (isset($report['timezone'])) {
			$reportTime = getLocalTimeFromGMT($report['reportdate'], $report['timezone']);
			$obsTime = getLocalTimeFromGMT($report['obsdate'], $report['timezone']);
			$tzAbbrev = getTzAbbrev($report['timezone']);
		} else {
			$reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['reportdate']);
			$obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['obsdate']);
			$tzAbbrev = "GMT";						
		}
		$report['reportTime'] = $reportTime;
		$report['obsTime'] = $obsTime;
		$report['tzAbbrev'] = $tzAbbrev;
		?>

		<li class="report <?= $options['showDetails'] ? 'expanded' : 'collapsed' ?>" reportid="<?= $report['id'] ?>" hasbuoys=<?= $options['locationHasBuoys'] ? "TRUE" : "FALSE" ; ?> hastide="<?= isset($report['tidestation']) ? $report['tidestation'] : 'FALSE' ; ?>" tz="<?=$report['timezone']?>" reporttime="<?=$reportTime?>" reporterid="<?=$report['reporterid']?>" imagepath="<? if(isset($report['imagepath'])) print $report['imagepath'] ?>">
			<ul>
				<li class="report-head">
					<a class="loc-name" href="<?=Path::toLocation($report['locationid']);?>"><?= html($report['locname'])?></a>
					<?
					if (isset($report['sl_name'])) {
						?>
						<span class="tz"><?= html($report['sl_name']) ?></span>
						<?
					}
					?>
					<span class="obs-time"><?=$obsTime?> <span class="tz"><?=$tzAbbrev?></span></span>
				</li>
				<? 
				if (isset($report['quality'])) {
					$qualities = ReportOptions::quality();
					$text = $qualities[$report['quality']];
					?>
					<li class="quality rating<?= $report['quality'] ?>">
						<ul class="ratings">
							<li title="<?= $qualities[1]; ?>" class="level one"></li>
							<li title="<?= $qualities[2]; ?>" class="level two"></li>
							<li title="<?= $qualities[3]; ?>" class="level three"></li>
							<li title="<?= $qualities[4]; ?>" class="level four"></li>
							<li title="<?= $qualities[5]; ?>" class="level five"></li>
						</ul>
						<span class="text"><?= html($text) ?> session</span>
					</li>
					<?
				}

				//render a thumbnail image on page load.
				if(isset($report['imagepath'])) { 
					self::renderImage($report['imagepath'], $thumb = TRUE);
				}
								
				if(isset($report['waveheight'])) { 
					$heights = ReportOptions::getWaveHeights();
					$height = $report['waveheight'];
					?>
					<li class="waveheight"><?= $heights[$height][0] . '-' . $heights[$height][1] . '&rdquo;' ?></li>
					<? 
				} 	

				if(isset($report['text'])) { 
					?>
					<li class="text-report"><?= bbcode2html($report['text']) ?></li>
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
				<? if ($options['locationHasBuoys']) { ?>
					<span class="buoy-icon icon" title="<?=$report['locname']?> has buoy stations">B</span>
				<? } ?>
				<? if (isset($report['tidestation'])) { ?>
					<span class="tide-icon icon" title="<?=$report['locname']?> has a tide station">T</span>
				<? } ?>		
			</span>	
			<div class="click-to-expand">
				<span>Click To</span>
				<div>Expand</div>
			</div>
		</li>
		<?		
	}	

	static function renderReportDetails($report, $options = array()) {
		if(isset($report['imagepath'])) { 
			self::renderImage($report['imagepath']);
		}					
		if ($options['locationHasBuoys']) {
			self::renderBuoyDetails($report['id'], $report['timezone']);
		}	
		if (isset($report['tidestation'])) {
			self::renderTideDetails($report['id'], $report['tidestation'], $report['timezone']);
		}					
		self::renderReporterDetails($report['id'], $report['reporterid'], $report['reportTime'], $report['timezone']);
	}

	public static function renderImage($imagepath, $thumbnail=FALSE) {
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

	public static function renderBuoyDetails($reportid, $tz) {
		$buoyDataRows = Persistence::getBuoyData($reportid);
		if (isset($buoyDataRows)) {
			?>
			<ul class="buoy-data">
				<? foreach ($buoyDataRows as $buoyDataRow) { 
					$buoyInfo = Persistence::getBuoyInfo($buoyDataRow['buoy']); //inner join this
					$localBuoyTime = getLocalTimeFromGMT($buoyDataRow['gmttime'], $tz);
					$tzAbbrev = getTzAbbrev($tz);
					?>
					<li class="buoy-data-item">
						<span class="station-head">
							<span>Buoy </span>
							<a target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=$buoyDataRow['buoy']?>"><?=$buoyDataRow['buoy']?></a>

							<? if (isset($buoyInfo['name'])) { ?>
								<span>(<?= html($buoyInfo['name'])?>)</span>
							<? } ?>
						</span>
						<ul class="station-data-fields">
							<li><span class="label">Time of Report:</span> <?=$localBuoyTime?> (<?=$tzAbbrev?>)</li>
							<? 
							if (isset($buoyDataRow['winddir'])) { 
								?>
									<li>
										<span class="label">Wind Direction:</span> <?=$buoyDataRow['winddir']?>&deg; (<?=getDirection($buoyDataRow['winddir'])?>)
									</li>
								<? 
							}
							if (isset($buoyDataRow['windspeed'])) { 
								?>
									<li>
										<span class="label">Wind Speed:</span> <?=$buoyDataRow['windspeed']?><span> mph</span>
									</li>
								<? 
							} 
							if (isset($buoyDataRow['swellheight'])) { 
								?>
									<li>
										<span class="label">Swell Height:</span> <?=$buoyDataRow['swellheight']?><span> ft</span>
									</li>
								<? 
							}
							if (isset($buoyDataRow['swellperiod'])) { 
								?>
									<li>
										<span class="label">Swell Period:</span> <?=$buoyDataRow['swellperiod']?><span> secs</span>
									</li>
								<? 
							}
							if (isset($buoyDataRow['swelldir'])) { 
								?>
									<li>
										<span class="label">Swell Direction:</span> <?=$buoyDataRow['swelldir']?>&deg; (<?=getDirection($buoyDataRow['swelldir'])?>)
									</li>
								<? 
							} 
							if (isset($buoyDataRow['tide'])) { 
								?>
									<li>
										<span class="label">Swell Direction:</span> <?=$buoyDataRow['tide']?><span> ft</span>
									</li>
								<? 
							} 												
							?>
						</ul>
					</li>
				<? } ?>
			</ul>
			<? 
		}															
	}

	public function renderTideDetails($reportid, $station, $tz) {
		$tideData = Persistence::getTideData($reportid);
		if (isset($tideData) && abs($tideData['tide']) != 99.99) {
			$localTideTime = getLocalTimeFromGMT($tideData['tidedate'], $tz);
			$tzAbbrev = getTzAbbrev($tz);
			?>
			<span class="station-head">Tide Station <a href="http://tidesonline.noaa.gov/plotcomp.shtml?station_info=<?=$station?>" target="_blank"><?=html($station)?></a></span>
			<ul class="station-data-fields">
				<li><span class="label">Time of Report:</span> <?=$localTideTime?> (<?=$tzAbbrev?>)</li>
				<li><span class="label">Tide:</span> <?=$tideData['tide']?><span> ft</span></li>
				<li><span class="label">Tide Res:</span> <?=$tideData['tideres'] > 0 ? 'Incoming' : 'Outgoing'; ?></li>
			</ul>
			<?	
		}	
	}

	public function renderReporterDetails($reportId, $reporterid, $reportTime, $tz) {
		$reporterInfo = Persistence::getUserInfoById($reporterid);
		$tzAbbrev = getTzAbbrev($tz);
		?>
		<div class="reporter-details">
			<a href="<?=Path::toSinglePost($reportId)?>">report #<?=$reportId?></a> by <a href="<?=Path::toProfile($reporterid);?>"><?= html($reporterInfo['name']); ?></a> on <?= $reportTime ?> <span class="tz">(<?=$tzAbbrev?>)</span>
		</div>
		<?
	}

	public static function renderComments($comments) {
		
		foreach ($comments as $comment) {
			print $comment;
		}
	}


}

?>