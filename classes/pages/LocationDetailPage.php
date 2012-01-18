<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/BouyData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/TideData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddBouyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddTideStationForm.php';





class LocationDetailPage extends GeneralPage {

	public function loadData() {
		parent::loadData();
		$this->locationId = $_GET['location'];
		$this->locInfo = Persistence::getLocationInfoById($this->locationId);
		if (!isset($this->locInfo)) {
			header('Location:'.Paths::to404());
			exit();
		}			
		
		//ajax this
		$this->creator = Persistence::getReporterInfoById($this->locInfo['creator']);
		$this->foreCastLinks = array();			
		
		$this->bouys = array();
		$this->bouyCount = 0;
		if (isset($this->locInfo['bouy1'])) {
			$this->bouys['bouy1'] = $this->locInfo['bouy1'];
			$this->bouyCount = 1;
		}	
		if (isset($this->locInfo['bouy2'])) {
			$this->bouys['bouy2'] = $this->locInfo['bouy2'];
			$this->bouyCount = 2;
		}
		if (isset($this->locInfo['bouy3'])) {
			$this->bouys['bouy3'] = $this->locInfo['bouy3'];
			$this->bouyCount = 3;
		}	

		if ($this->userIsLoggedIn && Persistence::userHasLocation($this->userId, $this->locationId)) {
			$this->userHasLocation = TRUE;
		} else $this->userHasLocation = FALSE;		
		
		$this->addBouyError = NULL;
		$this->addStationError = NULL;

		$this->pageTitle = $this->locInfo['locname'];
	}

	public function getBodyClassName() {
		return 'location-detail-page';
	}		

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$("#add-bouy-form").validate();
				$("#add-tide-station-form").validate();
			});
		</script>
		<?
	}	

	public function afterSubmit() {

		if ($_REQUEST['submit'] == 'enter-bouy') {

			if (empty($_POST['bouy-id'])) {
				$this->addBouyError = "Please fill in bouy number";
				$this->renderPage();
				exit();
			}

			if (!$this->isValidBouy($_POST['bouy-id'])) {
				$this->renderPage();
				exit();				
			}

			Persistence::insertBouy($_POST['bouy-id'], $_POST['bouy-name'], $this->locationId, $this->bouyCount + 1);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'existingbouy' && isset($_GET['bouy'])) {
			
			if (!$this->isValidBouy($_GET['bouy'], FALSE)) {
				$this->renderPage();
				exit();				
			}

			Persistence::insertBouy($_GET['bouy'], "", $this->locationId, $this->bouyCount + 1, $checkDb = FALSE);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();	

		}

		if ($_REQUEST['submit'] == 'enter-tide-station') {

			//make this one js
			if (empty($_POST['station-id'])) {
				$this->addStationError = "Please enter a station";
				$this->renderPage();
				exit();
			}

			if (!$this->isValidTideStation($_POST['station-id'])) {
				$this->renderPage();
				exit();				
			}			
			
			Persistence::insertTideStation($_POST['station-id'], $_POST['station-name'], $this->locationId);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'existingtide' && isset($_GET['tidestation'])) {
			
			if (!$this->isValidTideStation($_GET['tidestation'], FALSE)) {
				$this->renderPage();
				exit();				
			}	

			Persistence::insertTideStation($_GET['tidestation'], "", $this->locationId, $checkDb = FALSE);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();
		}		

		if ($_REQUEST['submit'] == 'bookmark') {
			Persistence::insertUserLocation($this->userId, $this->locationId);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'un-bookmark') {
			Persistence::removeLocationFromUser($this->locationId, $this->userId);
			header('Location:'.Paths::toLocation($this->locationId));
			exit();
		}		
	}

	public function isValidBouy($bouy, $checkIfOnline = TRUE){	
		
		if ($checkIfOnline) {
			//dont need time (0) because were just checking if data file exists/is online		
			$bouyData = new BouyData($bouy, 0);
			if (!$bouyData->bouyExists) {
				$this->addBouyError = "Bouy " . $bouy . " cannot be reached";
				return FALSE;
			}	
		}	
		if (in_array($bouy, $this->bouys)) {
			$this->addBouyError = "Bouy " . $bouy . " is already set up for this location";
			return FALSE;			
		}
		return TRUE;
	}

	public function isValidTideStation($tideStation, $checkIfOnline = TRUE){	

		if ($checkIfOnline) {
			$tide = new TideData($tideStation, 0);
			if (!$tide->stationExists) {
				$this->addStationError = "Station " . $tideStation . " cannot be reached";
				return FALSE;	
			}				
		}
		return TRUE;
	}	
	
	public function renderLeft() {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				$filterform = new FilterForm;
				$options['location-page'] = $this->locationId;
				$filterform->renderFilterForm($options);
				?>
			</div>
		</div>
		<?
	}
	
	public function renderMain() {
		$this->renderLocDetails();		
		$this->renderLocReports();	

	}	

	public function renderLocDetails() {
		?>
		<div class="loc-details">
			<h1><?= html($this->locInfo['locname'])?></h1>
			<? if ($this->userIsLoggedIn) { 
				?><a class="post-report edit-loc-link  block-link" href="<?=Paths::toPostReport($this->locationId);?>">Post Report</a><? 
			}
			
			if ($this->bouyCount < 3 && $this->userIsLoggedIn) {
				?><span id="add-bouy-btn" class="edit-loc-link block-link" style="<?= isset($addBouyError) ? 'display:none;' : '' ?>">+ Bouy</span><?
			}

			if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
				?><span id="add-tide-station-btn" class="edit-loc-link block-link" style="<?= isset($addStationError) ? 'display:none;' : '' ?>">+ Tide Station</span><?
			}
			?>
			<div class="add-station-container">
				<?
				if ($this->bouyCount < 3 && $this->userIsLoggedIn) {
					$bform = new AddBouyForm;
					$bform -> renderAddBouyForm($this->addBouyError);
				}
				if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
					$tform = new AddTideStationForm;
					$tform -> renderAddTideStationForm($this->addStationError);
				}
				?>
				<script type="text/javascript"> 
					(function(){
						$('#add-bouy-btn').click(function(event){
							$('#add-tide-station-div').hide();
							$('#add-bouy-div').toggle();
							$('#add-tide-station-btn').removeClass('active');
							$('#add-bouy-btn').toggleClass('active');
						});
						$('#add-tide-station-btn').click(function(event){
							$('#add-bouy-div').hide();
							$('#add-tide-station-div').toggle();
							$('#add-bouy-btn').removeClass('active');
							$('#add-tide-station-btn').toggleClass('active');
						});
						$('#add-existing-bouy').click(function(event){
							$('#existing-bouys-container').toggle().addClass('loading');
							$('#existing-bouys-container').load('<?=Paths::toAjax()?>existing-stations.php?stationType=bouy&locationid=<?=$this->locationId?>',
								function(){
									$('#existing-bouys-container').removeClass('loading');
								}
							);
						});
						$('#add-existing-tidestation').click(function(event){
							$('#existing-tidestation-container').toggle().addClass('loading');
							$('#existing-tidestation-container').load('<?=Paths::toAjax()?>existing-stations.php?stationType=tidestation&locationid=<?=$this->locationId?>',
								function(){
									$('#existing-tidestation-container').removeClass('loading');
								}
							);
						});						
					})()
				</script>				
			</div>
		</div>
		<?
	}

	private function renderLocReports() {
		?>
		<div class="reports-container">
			<h3>Reports</h3>
			<?
			$options['locations'] = array($this->locInfo);
			$options['on-page'] = 'location-page';			
			$reports = new ReportFeed;
			$reports->loadData($options);	
			$reports->renderFilterIcon();	
			?>
			<div id="report-feed-container">		
				<? $reports->renderReportFeed(); ?>
			</div>
			<?
			$reports->renderReportFeedJS();
			?>			
		</div>
		<?
	} 

	public function renderRight() {

		$this->renderCurrentData();
		$this->renderLocationInfo();

	}

	private function renderCurrentData() {
		?>
		<div class="sidebar-section">	
			<h3>Current Data</h4>
			<div class="current-data">
				<div class="tidestation-data">	
					<span>Tide Station:	</span>
					<?
					if (isset($this->locInfo['tidestation'])) {
						$stationInfo = Persistence::getTideStationInfo($this->locInfo['tidestation']);
						?>
						<a target="_blank" href="http://tidesonline.noaa.gov/plotcomp.shtml?station_info=<?=$this->locInfo['tidestation']?>"><?=$this->locInfo['tidestation']?></a>
						<? 
						if(isset($stationInfo['stationname'])) { 
							?>
							<span> (<?= $stationInfo['stationname'] ?>)</span>
							<? 
						}	
					} else {
						?> 
						<span class="no-data">No tide station</span>
						<?
					}
					?>
				</div>
				<div>	
					<span>Bouy Stations:</span>					
					<?			
					if ($this->bouyCount > 0) {
						?>
						<ul>
						<?
						foreach($this->bouys as $bouy){
							$bouyInfo = Persistence::getBouyInfo($bouy);
							?>
							<li>
								<a class="bouy-iframe-link block-link" target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=html($bouy)?>"><? 
								if (isset($bouyInfo['name'])) { 
									?>
									<span><?= html($bouyInfo['name'])?></span>
									<? 
								} else {
									?>
									<span><?= html($bouy) ?></span>
									<?
								}														
								?></a>
								<iframe src="http://www.ndbc.noaa.gov/widgets/station_page.php?station=<?=html($bouy)?>" style="width:100%; height:300px"></iframe>
							</li>								
							<?
							}

						?>
						</ul>
						<?
					} else {
						?> 
						<span class="no-data">No bouys
							<?
							if (!$this->userIsLoggedIn) {
								?><span class="must-log-in">- You must be logged in to add bouys or stations</span><?
							}
							?>
						</span>
						<?
					}
				?>
				</div>
			</div>
		</div>
		<?
	}

	private function renderLocationInfo() {
		?>			
		<div class="loc-meta sidebar-section">
			<h3>Location Info</h3>
			<div class="reporters">
				<p class="creator">Set up by <a href="<?=Paths::toProfile($this->locInfo['creator']);?>"><?=$this->creator['name']?></a></p>
				<p><a href="<?=Paths::toReporters($this->locationId);?>">See Reporters</a></p>
				<?
				if ($this->userIsLoggedIn && $this->locInfo['creator'] == $this->userId) {
					?><a class="edit-location" href="<?=Paths::toEditLocation($this->locationId);?>">Edit Location</a><?
				}

				if ($this->userIsLoggedIn && $this->userHasLocation == FALSE) {
					?>
					<form action="" method="post" class="bookmark">
						<input type="hidden" name="submit" value="bookmark"/>
						<input type="submit" name="bookmark" value="Add To My Locations"/>
					</form>
					<?
				}

				else if ($this->userIsLoggedIn && $this->userHasLocation == TRUE) {
					?>
					<form action="" method="post" class="bookmark">
						<input type="hidden" name="submit" value="un-bookmark"/>
						<input type="submit" name="un-bookmark" value="Remove from my Locations"/>
					</form>						
					<?
				}

			?>
			</div>
		</div>	
		<?
	}
	


}