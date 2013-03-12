<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/view/AddBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddTideStationForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/FilterNote.php';



class LocationDetailPage extends Page {

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
		$this->location = LocationService::getLocation($this->locationId, array(
			'includeSublocations' => true,
			'includeBuoys' => true,
			'includeTideStations' => true
		));
		if (!isset($this->location)) {
			header('Location:'.Path::to404());
			exit();	
		}

		$this->location->sublocations = Persistence::getSubLocationsByLocation($this->locationId);
		
		//ajax this
		$this->creator = Persistence::getUserInfoById($this->location->creator);
		

		if ($this->user->isLoggedIn && Persistence::userHasLocation($this->user->id, $this->locationId)) {
			$this->userHasLocation = TRUE;
		} else $this->userHasLocation = FALSE;		

		$this->pageTitle = $this->location->locname;


		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $this->addBuoyError = "Please fill in buoy number"; break;
				case 2: $this->addStationError = "Please fill in station number"; break;
			}
		}
		if (isset($_GET['be1']) && $_GET['be1']) {
			$this->addBuoyError = "Buoy " . html($_GET['be1']) . " is already set up for this location";
		}
		if (isset($_GET['be2']) && $_GET['be2']) {
			$this->addBuoyError = "Buoy " . html($_GET['be2']) . " cannot be reached";
		}
		if (isset($_GET['te']) && $_GET['te']) {
			$this->addStationError = "Station " . html($_GET['te']) . " cannot be reached";	
		}
		if (isset($_GET['entered']) && $_GET['entered'] == 'buoy') {
			$this->enteredBuoy = TRUE;
		}
		if (isset($_GET['entered']) && $_GET['entered'] == 'tide') {
			$this->enteredTide = TRUE;
		}	
		
		/* load Report Filters */
		$this->reportFilters = array();
		$this->reportFilters['quality'] 	  = $_REQUEST['quality'];
		$this->reportFilters['image']   	  = $_REQUEST['image'];
		$this->reportFilters['text']    	  = $_REQUEST['text'];
		$this->reportFilters['date']    	  = $_REQUEST['date'];
		$this->reportFilters['subLocationId'] = $_REQUEST['subLocationId'];
		$this->reportFilters['location'] 	  = $this->location->id;

		/* load Reports */
		//$this->reports = Persistence::getReports($this->reportFilters);					
		$this->reports = ReportService::getReportsForFilters($this->reportFilters);
							
	}

	public function getBodyClassName() {
		return 'location-detail-page';
	}	
	

	private function renderLocReports() {
		?>
		<div class="reports-container">
			<h2>Recent Reports</h2>		
			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<? 
				FilterNote::renderFilterNote(array_merge($this->reportFilters, array(
					'location'=> $this->location->locname
				)));
				ReportFeed::renderFeed($this->reports);
				?>
			</div>
		</div>		
		<?
	} 
	
	public function renderLeft() {
		$filterOptions = array(
			'sublocationObjects' => $this->location->sublocations
		);
		FilterForm::renderFilterModule($filterOptions, array('location'=>$this->location->id));
	}
				

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$("#add-buoy-form").validate();
				$("#add-tide-station-form").validate();
				doFcLinkAjax('');
			});

			function doFcLinkAjax(newUrl) {
				linkContainer = $('#fc-link-container');
				if (newUrl != '') {
					data = {url:newUrl}
				} else {
					data = {}
				}
				if (linkContainer.hasClass('loaded') && newUrl == '') return;
				linkContainer.addClass('loading');
				linkContainer.load('<?=Path::toAjax()?>location-info.php?info=forecast&locationid=<?=$this->locationId?>', 
					
					data,			
					function(){
						linkContainer.removeClass('loading').addClass('loaded');
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
					url: "<?=Path::toAjax()?>location-info.php?info=deletelinks&locationid=<?=$this->locationId?>",
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

		if ($_REQUEST['submit'] == 'bookmark') {
			Persistence::insertUserLocation($this->user->id, $this->locationId);
			header('Location:'.Path::toLocation($this->locationId));
			exit();
		}

		if ($_REQUEST['submit'] == 'un-bookmark') {
			Persistence::removeLocationFromUser($this->locationId, $this->user->id);
			header('Location:'.Path::toLocation($this->locationId));
			exit();
		}		
	}

	protected function handleStationSubmission($to = 'toLocation') {

		if ($_REQUEST['submit'] == 'enter-buoy') {

			if (empty($_POST['buoy-id'])) {	
				$error = 1;
				header('Location:'.Path::$to($this->locationId, $error));
				exit();		
			}

			if (!$this->isValidBuoy($_POST['buoy-id'])) {
				if ($this->addBuoyError == "buoy-exists") {
					header('Location:'.Path::$to($this->locationId).'&be1='.urlencode($_POST['buoy-id']));
					exit();						
				}
				if ($this->addBuoyError == "buoy-offline") {
					header('Location:'.Path::$to($this->locationId).'&be2='.urlencode($_POST['buoy-id']));
					exit();						
				}				
			}

			Persistence::insertBuoy($_POST['buoy-id'], $_POST['buoy-name'], $this->locationId, $this->buoyCount + 1);
			header('Location:'.Path::$to($this->locationId).'&entered=buoy');
			exit();
		}

		if ($_REQUEST['submit'] == 'existingbuoy' && isset($_GET['buoy'])) {
			
			if (!$this->isValidBuoy($_GET['buoy'], FALSE)) {
				header('Location:'.Path::$to($this->locationId).'&be1='.urlencode($_GET['buoy']));
				exit();					
			}

			Persistence::insertBuoy($_GET['buoy'], "", $this->locationId, $this->buoyCount + 1, $checkDb = FALSE);
			header('Location:'.Path::$to($this->locationId).'&entered=buoy');
			exit();	

		}

		if ($_REQUEST['submit'] == 'enter-tide-station') {

			//make this one js
			if (empty($_POST['station-id'])) {
				$error = 2;
				header('Location:'.Path::$to($this->locationId, $error));
				exit();	
			}

			if (!$this->isValidTideStation($_POST['station-id'])) {
				header('Location:'.Path::$to($this->locationId).'&te='.urlencode($_POST['station-id']));
				exit();						
		
			}			
			
			Persistence::insertTideStation($_POST['station-id'], $_POST['station-name'], $this->locationId);
			header('Location:'.Path::$to($this->locationId).'&entered=tide');
			exit();
		}

		if ($_REQUEST['submit'] == 'existingtide' && isset($_GET['tide'])) {

			/*			
			if (!$this->isValidTideStation($_GET['tide'])) {
				header('Location:'.Path::$to($this->locationId).'&te='.urlencode($_POST['station-id']));
				exit();					
			}	
			*/

			Persistence::insertTideStation($_GET['tide'], "", $this->locationId, $checkDb = FALSE);
			header('Location:'.Path::$to($this->locationId).'&entered=tide');
			exit();
		}	
		
			
		if ($_POST['submit'] == 'remove-buoy') {
			Persistence::removeBuoyFromLocation($_POST['key'], $this->buoys, $this->locationId);
			header('Location:'.Path::$to($this->locationId));
			exit();			
		}

		if ($_POST['submit'] == 'remove-tide-station') {
			Persistence::removeTideStationFromLocation($_POST['tidestation'], $this->locationId);
			header('Location:'.Path::$to($this->locationId));
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
			$buoyData = new Buoy($buoy);
			if (!$buoyData->isValid()) {
				$this->addBuoyError = "buoy-offline";
				return FALSE;
			}	
		}	
		return TRUE;
	}

	public function isValidTideStation($tideStation){	
		$station = new TideStation($tideStation);
		if (!$station->isValid()) {
			$this->addStationError = "station-offline";
			return FALSE;	
		}				
		return TRUE;
	}	
	
	public function renderMain() {
		$this->renderLocDetails();		
		$this->renderLocReports();	

	}	

	public function renderLocDetails() {
		?>
		<div class="loc-details">
			<h1><?= html($this->location->locname)?></h1>
			<?
			if (count($this->location->buoys) < 3 && $this->user->isLoggedIn) {
				?><span id="add-buoy-btn" class="edit-loc-link block-link <?=isset($this->addBuoyError) ? 'active' : ''?>">Edit Buoys</span><?
			}

			if (count($this->location->tideStations) < 2 && $this->user->isLoggedIn) {
				?><span id="add-tide-station-btn" class="edit-loc-link block-link <?=isset($this->addStationError) ? 'active' : ''?>">Add Tide Station</span><?
			}
			?>
			<a class="post-report edit-loc-link block-link" href="<?=Path::toPostReport($this->locationId);?>">Post Report</a>
			<?


			$this->renderAddStationContainers();

			?>
		</div>
		<?
	}

	protected function renderAddStationContainers($to = 'toLocation') {	
		?>
		<div class="add-station-container">
			<?
			AddBuoyForm::render(array(
				'status'=>$this->addBuoyError,
				'location'=>$this->location
			));

			$tform = new AddTideStationForm;
			$tform -> renderAddTideStationForm($this->addStationError);
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
					$('#add-existing-tidestation').click(function(event){
						$('#existing-tidestation-container').toggle().addClass('loading');
						$('#existing-tidestation-container').load('<?=Path::toAjax()?>existing-stations.php?stationType=tide&locationid=<?=$this->locationId?>&to=<?=$to?>',
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
					<? if ($this->user->isLoggedIn) { ?>
						<div class="enter-link-form">
							<input type="url" id="fc-url" class="text-input" placeholder="Add Link"/>
							<input type="submit" name="add-forecast" id="submit-fc-btn" value="Add Link"/>
						</div>
					<? } 

					if ($this->SingleReportPage == $this->user->id) { ?>
						<div class="edit-link-btns">
							<span class="edit-link-btn" id="delete-link-btn">Delete links</span>	
							<span class="edit-link-btn" id="delete-link-cancel" style="display:none">Cancel</span>
						</div>		
					<? } ?>
				</div>
				<script type="text/javascript">					
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
				<h5 class="toggle-btn">Tide Stations &darr;</h5>
				<div class="toggle-area" style="<?=$this->enteredTide ? 'display:block' : '';?>">
					<?
					foreach($this->location->tideStations as $tideStation) {
						?>
						<p><a target="_blank" href="<?=Path::toNOAATideStation($tideStation->stationid)?>"><?=$tideStation->stationid?></a> (<?= $tideStation->stationname ?>)</p>
						<?
					}
					?>
				</div>
			</div>
			<div class="buoy-current-data sb-section">	
				<h5 class="toggle-btn">Buoy Stations &darr;</h5>
				<div class="toggle-area" style="<?=$this->enteredBuoy ? 'display:block' : '';?>">
					<?
					foreach($this->location->buoys as $buoy){
						?>
						<div>
							<a class="buoy-iframe-link" target="_blank" href="<?=Path::toNOAABuoy($buoy->buoyid)?>"><?
								print isset($buoy->name) ? html($buoy->name) : html($buoy->buoyid) 													
							?></a>
							<iframe src="http://www.ndbc.noaa.gov/widgets/station_page.php?station=<?=$buoy->buoyid?>" style="width:100%; min-height: 300px"></iframe>
						</div>								
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
		<div class="loc-meta">
			<h3>Location Info</h3>
			<div class="reporters">
				<p class="creator sb-section">Set up by <a href="<?=Path::toProfile($this->location->creator);?>"><?=html($this->creator['name'])?></a></p>
				<p class="sb-section"><a href="<?=Path::toReporters($this->locationId);?>">See Reporters</a></p>
				<p class="sb-section"><a class="edit-location" href="<?=Path::toEditLocation($this->locationId);?>">Edit Location</a></p>

				<?
				if ($this->user->isLoggedIn && $this->userHasLocation == FALSE) {
					?>
					<form action="" method="post" class="bookmark">
						<input type="hidden" name="submit" value="bookmark"/>
						<input type="submit" name="bookmark" value="Add To My Locations"/>
					</form>
					<?
				}

				else if ($this->user->isLoggedIn && $this->userHasLocation == TRUE) {
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