<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/BouyData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/TideData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddBouyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddTideStationForm.php';


class LocationDetailPage extends GeneralPage {

	protected $addBouyError = NULL;
	protected $addStationError = NULL;
	protected $bouys = array();
	protected $bouyCount = 0;
	protected $forecastLinks = array();

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

		$this->pageTitle = $this->locInfo['locname'];


		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $this->addBouyError = "Please fill in bouy number"; break;
				case 2: $this->addStationError = "Please fill in station number"; break;
			}
		}
		if (isset($_GET['be1']) && $_GET['be1']) {
			$this->addBouyError = "Bouy " . $_GET['be1'] . " is already set up for this location";
		}
		if (isset($_GET['be2']) && $_GET['be2']) {
			$this->addBouyError = "Bouy " . $_GET['be2'] . " cannot be reached";
		}
		if (isset($_GET['te']) && $_GET['te']) {
			$this->addStationError = "Station " . $_GET['te'] . " cannot be reached";	
		}		
							
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

			function doFcLinkAjax(newUrl) {
				elem = $('#fc-link-container');
				if (newUrl != '') {
					data = {url:newUrl}
				} else {
					data = {}
				}
				if (elem.hasClass('loaded') && newUrl == '') return;
				elem.addClass('loading');
				elem.load('<?=Paths::toAjax()?>location-info.php?info=forecast&locationid=<?=$this->locationId?>', 
					
					data,			
					
					function(){
						elem.removeClass('loading').addClass('loaded');
					}
				);
				return false;				
			}	

			function cancelDeleteLinks() {
				$('.delete-link-check').hide();
				$('#delete-link-cancel').hide();
				$('#add-fc-link-form').show();
				$('#delete-link-btn').text('Delete links').removeClass('ready');
			}

			function deleteCheckedLinks(links) {
				elems = $('.delete-link-check:checked');
				links = [];

				elems.each(function(){
					links.push($(this).val());
				});

				$.ajax({
					url: "<?=Paths::toAjax()?>location-info.php?info=deletelinks&locationid=<?=$this->locationId?>",
					type: "GET",
					data: { links : links },
					cache: false,
					success: function() {
						elems.closest('.fc-link').remove();
						cancelDeleteLinks();
					} 
				});
			}					
		</script>
		<?
	}	

	public function afterSubmit() {

		$this->handleStationSubmission('toLocation');

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

	protected function handleStationSubmission($to = 'toLocation') {

		if ($_REQUEST['submit'] == 'enter-bouy') {

			if (empty($_POST['bouy-id'])) {	
				$error = 1;
				header('Location:'.Paths::$to($this->locationId, $error));
				exit();		
			}

			if (!$this->isValidBouy($_POST['bouy-id'])) {
				if ($this->addBouyError == "bouy-exists") {
					header('Location:'.Paths::$to($this->locationId).'&be1='.$_POST['bouy-id']);
					exit();						
				}
				if ($this->addBouyError == "bouy-offline") {
					header('Location:'.Paths::$to($this->locationId).'&be2='.$_POST['bouy-id']);
					exit();						
				}				
			}

			Persistence::insertBouy($_POST['bouy-id'], $_POST['bouy-name'], $this->locationId, $this->bouyCount + 1);
			header('Location:'.Paths::$to($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'existingbouy' && isset($_GET['bouy'])) {
			
			if (!$this->isValidBouy($_GET['bouy'], FALSE)) {
				header('Location:'.Paths::$to($this->locationId).'&be2='.$_GET['bouy']);
				exit();					
			}

			Persistence::insertBouy($_GET['bouy'], "", $this->locationId, $this->bouyCount + 1, $checkDb = FALSE);
			header('Location:'.Paths::$to($this->locationId));
			exit();	

		}

		if ($_REQUEST['submit'] == 'enter-tide-station') {

			//make this one js
			if (empty($_POST['station-id'])) {
				$error = 2;
				header('Location:'.Paths::$to($this->locationId, $error));
				exit();	
			}

			if (!$this->isValidTideStation($_POST['station-id'])) {
				header('Location:'.Paths::$to($this->locationId).'&te='.$_POST['station-id']);
				exit();						
		
			}			
			
			Persistence::insertTideStation($_POST['station-id'], $_POST['station-name'], $this->locationId);
			header('Location:'.Paths::$to($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'existingtide' && isset($_GET['tidestation'])) {
			
			if (!$this->isValidTideStation($_GET['tidestation'], FALSE)) {
				header('Location:'.Paths::$to($this->locationId).'&te='.$_POST['station-id']);
				exit();					
			}	

			Persistence::insertTideStation($_GET['tidestation'], "", $this->locationId, $checkDb = FALSE);
			header('Location:'.Paths::$to($this->locationId));
			exit();
		}	
		
			
		if ($_POST['submit'] == 'remove-bouy') {
			Persistence::removeBouyFromLocation($_POST['key'], $this->bouys, $this->locationId);
			header('Location:'.Paths::$to($this->locationId));
			exit();			
		}

		if ($_POST['submit'] == 'remove-tide-station') {
			Persistence::removeTideStationFromLocation($_POST['tidestation'], $this->locationId);
			header('Location:'.Paths::$to($this->locationId));
			exit();			
		}		
			
		

	}

	public function isValidBouy($bouy, $checkIfOnline = TRUE){	
		
		if (in_array($bouy, $this->bouys)) {
			$this->addBouyError = "bouy-exists";
			return FALSE;			
		}		
		
		if ($checkIfOnline) {
			//dont need time (0) because were just checking if data file exists/is online		
			$bouyData = new BouyData($bouy, 0);
			if (!$bouyData->bouyExists) {
				$this->addBouyError = "bouy-offline";
				return FALSE;
			}	
		}	
		return TRUE;
	}

	public function isValidTideStation($tideStation, $checkIfOnline = TRUE){	

		if ($checkIfOnline) {
			$tide = new TideData($tideStation, 0);
			if (!$tide->stationExists) {
				$this->addStationError = "station-offline";
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
				?><span id="add-bouy-btn" class="edit-loc-link block-link <?=isset($this->addBouyError) ? 'active' : ''?>">+ Bouy</span><?
			}

			if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
				?><span id="add-tide-station-btn" class="edit-loc-link block-link <?=isset($this->addStationError) ? 'active' : ''?>">+ Tide Station</span><?
			}

			$this->renderAddStationContainers();

			?>
		</div>
		<?
	}

	protected function renderAddStationContainers() {	
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
		<div class="current-data">	
			<h3>Current Data</h4>
			<div class="fc-links sb-section">
				<h5 class="toggle-btn" id="toggle-fc-list">Forecast Links &darr;</h5>
				<div class="toggle-area">
					<div id="fc-link-container"></div>
					<? if ($this->userIsLoggedIn) { ?>

						<form id="add-fc-link-form" method="post">
							<input type="url" id="fc-url" class="text-input" placeholder="Add Link"/>
							<input type="hidden" name="locationid" value="<?=$this->locationId?>"/>
							<input type="hidden" name="submit" value="add-fc-link"/>
							<input type="submit" name="add-forecast" id="submit-fc-btn" value="Add Link"/>
						</form>		

					<? } 

					if ($this->userIsLoggedIn && $this->locInfo['creator'] == $this->userId) { ?>

						<span class="edit-link-btn" id="delete-link-btn">Delete links</span>	
						<span class="edit-link-btn" id="delete-link-cancel" style="display:none">Cancel</span>
								
					<? } ?>
				</div>
				<script type="text/javascript">
						$('#toggle-fc-list').click(function(){
							doFcLinkAjax('');
						});						
						$('#submit-fc-btn').click(function(){
							doFcLinkAjax($('#fc-url').val());
						});
						$('#delete-link-btn').click(function(){

							if ($(this).hasClass('ready')) {
								deleteCheckedLinks();
								return;
							}

							$('.delete-link-check').show();
							$('#add-fc-link-form').hide();
							$('#delete-link-cancel').show().bind('click', function(){
								cancelDeleteLinks();
							});
							$(this).text('Delete checked links').addClass('ready');
						});	

				</script>
			</div>
			<div class="tidestation-data sb-section">	
				<h5 class="toggle-btn">Tide Station <?= isset($this->locInfo['tidestation']) ? '&darr;' : '';?></h5>
				
				<?
				if (isset($this->locInfo['tidestation'])) {
					$stationInfo = Persistence::getTideStationInfo($this->locInfo['tidestation']);
					?>
					<div class="toggle-area">
						<a target="_blank" href="http://tidesonline.noaa.gov/plotcomp.shtml?station_info=<?=$this->locInfo['tidestation']?>"><?=$this->locInfo['tidestation']?></a>
						<? 
						if(isset($stationInfo['stationname'])) { 
							?>
							<span> (<?= $stationInfo['stationname'] ?>)</span>
							<? 
						}	
						?>
					</div>
					<?
				} else {
					?> 
					<span class="no-data">No tide station</span>
					<?
				}
				?>
			</div>
			<div class="bouy-current-data sb-section">	
				<h5 class="toggle-btn">Bouy Stations <?=$this->bouyCount > 0 ? '&darr;' : '';?></h5>
				<?			
				if ($this->bouyCount > 0) {
					?>
					<ul class="toggle-area">
					<?
					foreach($this->bouys as $bouy){
						$bouyInfo = Persistence::getBouyInfo($bouy);
						?>
						<li>
							<a class="bouy-iframe-link" target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=html($bouy)?>"><? 
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
					<span class="no-data">None
						<?
						if (!$this->userIsLoggedIn) {
							?><span class="must-log-in">- Log in to add bouys or stations</span><?
						}
						?>
					</span>
					<?
				}
				?>
			</div>
		</div>
		<?
	}

	private function renderLocationInfo() {
		?>			
		<div class="loc-meta">
			<h3>Location Info</h3>
			<div class="reporters">
				<p class="creator sb-section">Set up by <a href="<?=Paths::toProfile($this->locInfo['creator']);?>"><?=$this->creator['name']?></a></p>
				<p class="sb-section"><a href="<?=Paths::toReporters($this->locationId);?>">See Reporters</a></p>
				<?
				if ($this->userIsLoggedIn && $this->locInfo['creator'] == $this->userId) {
					?><p class="sb-section"><a class="edit-location" href="<?=Paths::toEditLocation($this->locationId);?>">Edit Location</a></p><?
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