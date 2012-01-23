<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/BuoyData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/TideData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddTideStationForm.php';


class LocationDetailPage extends GeneralPage {

	protected $addBuoyError = NULL;
	protected $addStationError = NULL;
	protected $buoys = array();
	protected $buoyCount = 0;
	protected $forecastLinks = array();
	protected $enteredBuoy = FALSE;
	protected $enteredTide = FALSE;

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
		
		if (isset($this->locInfo['buoy1'])) {
			$this->buoys['buoy1'] = $this->locInfo['buoy1'];
			$this->buoyCount = 1;
		}	
		if (isset($this->locInfo['buoy2'])) {
			$this->buoys['buoy2'] = $this->locInfo['buoy2'];
			$this->buoyCount = 2;
		}
		if (isset($this->locInfo['buoy3'])) {
			$this->buoys['buoy3'] = $this->locInfo['buoy3'];
			$this->buoyCount = 3;
		}	

		if ($this->userIsLoggedIn && Persistence::userHasLocation($this->userId, $this->locationId)) {
			$this->userHasLocation = TRUE;
		} else $this->userHasLocation = FALSE;		

		$this->pageTitle = $this->locInfo['locname'];


		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $this->addBuoyError = "Please fill in buoy number"; break;
				case 2: $this->addStationError = "Please fill in station number"; break;
			}
		}
		if (isset($_GET['be1']) && $_GET['be1']) {
			$this->addBuoyError = "Buoy " . $_GET['be1'] . " is already set up for this location";
		}
		if (isset($_GET['be2']) && $_GET['be2']) {
			$this->addBuoyError = "Buoy " . $_GET['be2'] . " cannot be reached";
		}
		if (isset($_GET['te']) && $_GET['te']) {
			$this->addStationError = "Station " . $_GET['te'] . " cannot be reached";	
		}
		if (isset($_GET['entered']) && $_GET['entered'] == 'buoy') {
			$this->enteredBuoy = TRUE;
		}
		if (isset($_GET['entered']) && $_GET['entered'] == 'tide') {
			$this->enteredTide = TRUE;
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
				$("#add-buoy-form").validate();
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

		if ($_REQUEST['submit'] == 'enter-buoy') {

			if (empty($_POST['buoy-id'])) {	
				$error = 1;
				header('Location:'.Paths::$to($this->locationId, $error));
				exit();		
			}

			if (!$this->isValidBuoy($_POST['buoy-id'])) {
				if ($this->addBuoyError == "buoy-exists") {
					header('Location:'.Paths::$to($this->locationId).'&be1='.$_POST['buoy-id']);
					exit();						
				}
				if ($this->addBuoyError == "buoy-offline") {
					header('Location:'.Paths::$to($this->locationId).'&be2='.$_POST['buoy-id']);
					exit();						
				}				
			}

			Persistence::insertBuoy($_POST['buoy-id'], $_POST['buoy-name'], $this->locationId, $this->buoyCount + 1);
			header('Location:'.Paths::$to($this->locationId).'&entered=buoy');
			exit();
		}

		if ($_REQUEST['submit'] == 'existingbuoy' && isset($_GET['buoy'])) {
			
			if (!$this->isValidBuoy($_GET['buoy'], FALSE)) {
				header('Location:'.Paths::$to($this->locationId).'&be1='.$_GET['buoy']);
				exit();					
			}

			Persistence::insertBuoy($_GET['buoy'], "", $this->locationId, $this->buoyCount + 1, $checkDb = FALSE);
			header('Location:'.Paths::$to($this->locationId).'&entered=buoy');
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
			header('Location:'.Paths::$to($this->locationId).'&entered=tide');
			exit();
		}

		if ($_REQUEST['submit'] == 'existingtide' && isset($_GET['tide'])) {
			
			if (!$this->isValidTideStation($_GET['tide'], FALSE)) {
				header('Location:'.Paths::$to($this->locationId).'&te='.$_POST['station-id']);
				exit();					
			}	

			Persistence::insertTideStation($_GET['tide'], "", $this->locationId, $checkDb = FALSE);
			header('Location:'.Paths::$to($this->locationId).'&entered=tide');
			exit();
		}	
		
			
		if ($_POST['submit'] == 'remove-buoy') {
			Persistence::removeBuoyFromLocation($_POST['key'], $this->buoys, $this->locationId);
			header('Location:'.Paths::$to($this->locationId));
			exit();			
		}

		if ($_POST['submit'] == 'remove-tide-station') {
			Persistence::removeTideStationFromLocation($_POST['tidestation'], $this->locationId);
			header('Location:'.Paths::$to($this->locationId));
			exit();			
		}		
			
		

	}

	public function isValidBuoy($buoy, $checkIfOnline = TRUE){	
		
		if (in_array($buoy, $this->buoys)) {
			$this->addBuoyError = "buoy-exists";
			return FALSE;			
		}		
		
		if ($checkIfOnline) {
			//dont need time (0) because were just checking if data file exists/is online		
			$buoyData = new BuoyData($buoy, 0);
			if (!$buoyData->buoyExists) {
				$this->addBuoyError = "buoy-offline";
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
			
			if ($this->buoyCount < 3 && $this->userIsLoggedIn) {
				?><span id="add-buoy-btn" class="edit-loc-link block-link <?=isset($this->addBuoyError) ? 'active' : ''?>">+ Buoy</span><?
			}

			if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
				?><span id="add-tide-station-btn" class="edit-loc-link block-link <?=isset($this->addStationError) ? 'active' : ''?>">+ Tide Station</span><?
			}

			$this->renderAddStationContainers();

			?>
		</div>
		<?
	}

	protected function renderAddStationContainers($to = 'toLocation') {	
		?>
		<div class="add-station-container">
			<?
			if ($this->buoyCount < 3 && $this->userIsLoggedIn) {
				$bform = new AddBuoyForm;
				$bform -> renderAddBuoyForm($this->addBuoyError);
			}
			if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
				$tform = new AddTideStationForm;
				$tform -> renderAddTideStationForm($this->addStationError);
			}
			?>
			<script type="text/javascript"> 
				(function(){
					$('#add-buoy-btn').click(function(event){
						$('#add-tide-station-div').hide();
						$('#add-buoy-div').toggle();
						$('#add-tide-station-btn').removeClass('active');
						$('#add-buoy-btn').toggleClass('active');
					});
					$('#add-tide-station-btn').click(function(event){
						$('#add-buoy-div').hide();
						$('#add-tide-station-div').toggle();
						$('#add-buoy-btn').removeClass('active');
						$('#add-tide-station-btn').toggleClass('active');
					});
					$('#add-existing-buoy').click(function(event){
						$('#existing-buoys-container').toggle().addClass('loading');
						$('#existing-buoys-container').load('<?=Paths::toAjax()?>existing-stations.php?stationType=buoy&locationid=<?=$this->locationId?>&to=<?=$to?>',
							function(){
								$('#existing-buoys-container').removeClass('loading');
							}
						);
					});
					$('#add-existing-tidestation').click(function(event){
						$('#existing-tidestation-container').toggle().addClass('loading');
						$('#existing-tidestation-container').load('<?=Paths::toAjax()?>existing-stations.php?stationType=tide&locationid=<?=$this->locationId?>&to=<?=$to?>',
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
						<div class="enter-link-form">
							<input type="url" id="fc-url" class="text-input" placeholder="Add Link"/>
							<input type="submit" name="add-forecast" id="submit-fc-btn" value="Add Link"/>
						</div>
					<? } 

					if ($this->userIsLoggedIn && $this->locInfo['creator'] == $this->userId) { ?>
						<div class="edit-link-btns">
							<span class="edit-link-btn" id="delete-link-btn">Delete links</span>	
							<span class="edit-link-btn" id="delete-link-cancel" style="display:none">Cancel</span>
						</div>		
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
					<div class="toggle-area" style="<?=$this->enteredTide ? 'display:block' : '';?>">
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
			<div class="buoy-current-data sb-section">	
				<h5 class="toggle-btn">Buoy Stations <?=$this->buoyCount > 0 ? '&darr;' : '';?></h5>
				<?			
				if ($this->buoyCount > 0) {
					?>
					<ul class="toggle-area" style="<?=$this->enteredBuoy ? 'display:block' : '';?>">
					<?
					foreach($this->buoys as $buoy){
						$buoyInfo = Persistence::getBuoyInfo($buoy);
						?>
						<li>
							<a class="buoy-iframe-link" target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=html($buoy)?>"><? 
							if (isset($buoyInfo['name'])) { 
								?>
								<span><?= html($buoyInfo['name'])?></span>
								<? 
							} else {
								?>
								<span><?= html($buoy) ?></span>
								<?
							}														
							?></a>
							<iframe src="http://www.ndbc.noaa.gov/widgets/station_page.php?station=<?=html($buoy)?>" style="width:100%; height:300px"></iframe>
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
							?><span class="must-log-in">- Log in to add buoys or stations</span><?
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