<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Mobile_Detect.php';



class SingleReport {
	private $report;
	private $locationInfo;
	private $showDetails = FALSE;
	private	$locationHasBuoys = FALSE;
	private	$locationHasTide = FALSE;
	private	$tideStation = NULL;	
	private	$imagePath = '';
	private	$className = 'report';		

	public function loadData($report, $locationInfo = NULL, $showDetails = FALSE) {
		
		$this->report = $report;
		$this->showDetails = $showDetails;

		if (!isset($locationInfo)) {
			$this->locationInfo = Persistence::getLocationInfoById($report['locationid']);			
		} else {
			$this->locationInfo = $locationInfo;
		}


		if (isset($this->locationInfo['timezone'])) {
			$this->reportTime = getLocalTimeFromGMT($this->report['reportdate'], $this->locationInfo['timezone']);
			$this->obsTime = getLocalTimeFromGMT($this->report['obsdate'], $this->locationInfo['timezone']);
			$this->tzAbbrev = getTzAbbrev($this->locationInfo['timezone']);
		} else {
			$this->reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $this->report['reportdate']);
			$this->obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $this->report['obsdate']);
			$this->tzAbbrev = "GMT";						
		}


		if (isset($this->locationInfo['buoy1']) || isset($this->locationInfo['buoy2']) || isset($this->locationInfo['buoy3'])) {
			$this->locationHasBuoys = TRUE;
		}

		if (isset($this->locationInfo['tidestation'])) {
			$this->locationHasTide = TRUE;
			$this->tideStation = $locationInfo['tidestation'];
		}

		if (isset($this->report['imagepath'])) {
			$this->imagePath = $this->report['imagepath'];
		}

		if ($this->showDetails) {
			$this->className .= ' expanded';
		} else {
			$this->className .= ' collapsed';
		}
	}

	public function renderSingleReport() {
		?>
		<li class="<?=$this->className?>" reportid="<?= $this->report['id'] ?>" hasbuoys=<?= $this->locationHasBuoys ? "TRUE" : "FALSE" ; ?> hastide=<?= $this->locationHasTide ? "$this->tideStation" : "FALSE" ; ?> tz="<?=$this->locationInfo['timezone']?>" reporttime="<?=$this->reportTime?>" reporterid="<?=$this->report['reporterid']?>" imagepath="<?= $this->imagePath ?>">
			<ul>
				<li class="report-head">
					<a class="loc-name" href="<?=Paths::toLocation($this->report['locationid']);?>"><?= html($this->locationInfo['locname'])?></a>
					<span class="obs-time"><?=$this->obsTime?> <span class="tz">(<?=$this->tzAbbrev?>)</span></span>
				</li>
				<? 
				if (isset($this->report['quality'])) {
					$qualities = ReportOptions::quality();
					$text = $qualities[$this->report['quality']];
					?>
					<li class="quality rating<?= $this->report['quality'] ?>">
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
				if(isset($this->report['text'])) { 
					?>
					<li class="text-report">&ldquo;<?= bbcode2html($this->report['text']) ?>&rdquo;</li>
					<? 
				} 

				//loading buoy/tide details. setting up li's for ajax inserting. js will check if elems exist 
				?>	
				<li class="detail-section">
					<?
					/* rendered if on single page or new-report ajax */
					if ($this->showDetails) { 

						if(isset($this->report['imagepath'])) { 
							$this->renderImage($this->report['imagepath']);
						}					
						if ($this->locationHasBuoys) {
							$this->renderBuoyDetails($this->report['id'], $this->locationInfo['timezone']);
						}	
						if ($this->locationHasTide) {
							$this->renderTideDetails($this->report['id'], $this->tideStation, $this->locationInfo['timezone']);
						}					
						$this->renderReporterDetails($this->report['id'], $this->report['reporterid'], $this->reportTime, $this->locationInfo['timezone']);
					}
					?>
				</li>
			</ul>
			<span class="notification-icons">
				<? if ($this->locationHasBuoys) { ?>
					<span class="buoy-icon icon" title="<?=$this->locationInfo['locname']?> has buoy stations"></span>
				<? } ?>
				<? if ($this->locationHasTide) { ?>
					<span class="tide-icon icon" title="<?=$this->locationInfo['locname']?> has tide station"></span>
				<? } ?>	
				<? if ($this->imagePath != '') { ?>
					<span class="photo-icon icon" title="<?=$this->locationInfo['locname']?> has an image"></span>
				<? } ?>		
			</span>				
		</li>
		<?		
	}	

	public function renderImage($imagePath) {
		$detect = new Mobile_Detect();
		$detect->isSmallDevice() ? $dims = array(280,260) : $dims = array(508,400);
		$image = getImageInfo($imagePath, $dims[0], $dims[1]);
		if (!empty($image)) {
			?>
			<li class="image-container"><a href="<?=$image['src']?>" target="_blank"><image src="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/></a></li>
			<? 						
		}			
	}

	public function renderBuoyDetails($reportid, $tz) {
		$buoyDataRows = Persistence::getBuoyData($reportid);
		if (isset($buoyDataRows)) {
			?>
			<ul class="buoy-data">
				<? foreach ($buoyDataRows as $buoyDataRow) { 
					$buoyInfo = Persistence::getBuoyInfo($buoyDataRow['buoy']);
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
		$reporterInfo = Persistence::getReporterInfoById($reporterid);
		$tzAbbrev = getTzAbbrev($tz);
		?>
		<div class="reporter-details">
			<a href="<?=Paths::toSinglePost($reportId)?>">report #<?=$reportId?></a> by <a href="<?=Paths::toProfile($reporterid);?>"><?= html($reporterInfo['name']); ?></a> on <?= $reportTime ?> <span class="tz">(<?=$tzAbbrev?>)</span>
		</div>
		<?
	}


}

?>