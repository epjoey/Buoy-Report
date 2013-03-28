<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class LocationDetailPage extends Page {


	public function getBodyClassName() {
		$str = "location-detail-page ";
		$str .= $this->showReportForm ? 'show-report-form' : '';
		return $str;
	}	
	
	
	public function renderLeft() {
		$filterOptions = array(
			'sublocationObjects' => $this->location->sublocations
		);
		FilterForm::renderFilterModule($filterOptions, array('location'=>$this->location->id));
	}
	
	public function renderMain() {
		$this->renderLocDetails();		
		$this->renderLocReports();	

	}	

	public function renderLocDetails() {
		?>
		<div class="loc-details">
			<h1><a href="<?=Path::toLocation($this->location->id)?>"><?= html($this->location->locname)?></a></h1>
			<?
			if ($this->device->isSmallDevice()) {
				?>
				<div class="cover-photo">
				</div>
				<?
			}

			?>
			<div class="loc-report-section">
				<div class="loc-controls">
					<span id="add-buoy-btn" class="edit-loc-link block-link <?=isset($this->addBuoyError) ? 'active' : ''?>">Buoys</span><?
					?><span id="add-tide-station-btn" class="edit-loc-link block-link <?=isset($this->addStationError) ? 'active' : ''?>">Tide Stations</span><?
					?><a id="post-report-link" class="post-report edit-loc-link block-link" href="<?=Path::toPostReport($this->location->id);?>">Post Report</a>
				</div>
				<?
				ReportForm::renderReportForm($this->location, array(
					'statusMsg' => $this->reportFormStatus, //from session
					'needPicup' => $this->needPicup
				));
				AddBuoyForm::render(array(
					'status'=>null, //get from session
					'location'=>$this->location
				));
				AddTideStationForm::render(array(
					'status'=>null, //get from session
					'location'=>$this->location
				));
				?>
			</div>
			<script type="text/javascript"> 
				(function($){

					$('#add-buoy-btn').click(function(event){
						$(document.body).removeClass('show-report-form')
										.removeClass('show-add-tide-form')
										.toggleClass('show-add-buoy-form');
					});
					$('#add-tide-station-btn').click(function(event){
						$(document.body).removeClass('show-report-form')
										.removeClass('show-add-buoy-form')
										.toggleClass('show-add-tide-form');
					});					
					$('#post-report-link').click(function(event){
						event.preventDefault();
						$(document.body).removeClass('show-add-tide-form')
										.removeClass('show-add-buoy-form')
										.toggleClass('show-report-form');
					});
				})(jQuery);
			</script>					
		</div>
		<?
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
				ReportFeed::renderFeed($this->reports, array(
					'limit' => $this->numReportsPerPage,
					'feedLocation' => 'locationPage',
				));
				?>
			</div>
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
					<div class="enter-link-form">
						<input type="url" id="fc-url" class="text-input" placeholder="Add Link"/>
						<input type="submit" name="add-forecast" id="submit-fc-btn" value="Add Link"/>
					</div>
					<div class="edit-link-btns">
						<span class="edit-link-btn" id="delete-link-btn">Delete links</span>	
						<span class="edit-link-btn" id="delete-link-cancel" style="display:none">Cancel</span>
					</div>
				</div>
				<script type="text/javascript">
					(function($){
						BR.locationForecastLinks.doFcLinkAjax('');			
						
						$('#submit-fc-btn').click(function(){
							BR.locationForecastLinks.doFcLinkAjax($('#fc-url').val());
						});
						
						$('#delete-link-btn').click(function(){
							if ($(this).hasClass('ready')) {
								BR.locationForecastLinks.deleteCheckedLinks();
								return;
							}

							$('.delete-link-check').show();
							$('#add-fc-link-form').hide();
							$('#delete-link-cancel').show().bind('click', function(){
								BR.locationForecastLinks.cancelDeleteLinks();
							});
							$(this).text('Delete checked links').addClass('ready');
						});	
					})(jQuery);

				</script>
			</div>
			<div class="tidestation-data sb-section">
				<h5 class="toggle-btn">Tide Stations &darr;</h5>
				<div class="toggle-area" style="<?=$this->enteredTide || $this->showReportForm ? 'display:block' : '';?>">
					<?
					foreach($this->location->tideStations as $tideStation) {
						?>
						<p>
							<a target="_blank" href="<?=Path::toNOAATideStation($tideStation->stationid)?>"><?=$tideStation->stationid?></a> (<?= $tideStation->stationname ?>)
							<form action="<?=Path::toLocationRemoveTidestation()?>" method="post">
								<input type="hidden" name="stationid" value="<?=$tideStation->stationid?>"/>
								<input type="hidden" name="locationid" value="<?=$this->location->id?>"/>
								<input type="submit" name="remove-station" value="X"/>
							</form>
						</p>
						<?
					}
					?>
				</div>
			</div>
			<div class="buoy-current-data sb-section">	
				<h5 class="toggle-btn">Buoy Stations &darr;</h5>
				<div class="toggle-area" style="<?=$this->enteredBuoy || $this->showReportForm ? 'display:block' : '';?>">
					<?
					foreach($this->location->buoys as $buoy){
						?>
						<div>
							<div class="buoy-heading">
								<a class="buoy-iframe-link" target="_blank" href="<?=Path::toNOAABuoy($buoy->buoyid)?>"><?
									print isset($buoy->name) ? html($buoy->name) : html($buoy->buoyid) 													
								?></a>
								<form action="<?=Path::toLocationRemoveBuoy()?>" method="post">
									<input type="hidden" name="buoyid" value="<?=$buoy->buoyid?>"/>
									<input type="hidden" name="locationid" value="<?=$this->location->id?>"/>
									<input type="submit" name="remove-buoy" value="X"/>
								</form>
							</div>
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
				<p class="creator sb-section">Set up by <a href="<?=Path::toProfile($this->location->creator);?>"><?=html($this->creator->name)?></a></p>
				<p class="sb-section"><a href="<?=Path::toReporters($this->location->id);?>">See Reporters</a></p>
				<p class="sb-section"><a class="edit-location" href="<?=Path::toEditLocation($this->location->id);?>">Edit Location</a></p>

				<?
				if ($this->user->isLoggedIn) {
					?>
					<form action="<?=Path::toBookmarkLocation()?>" method="post" class="bookmark">
						<input type="hidden" name="locationId" value="<?= $this->location->id ?>"/>
						<?
						if (!in_array($this->location->id, Utils::pluck($this->user->locations, 'id'))) {
							?>	
							<input type="submit" name="bookmark" value="Add To My Locations"/>
							<?
						} else {
							?>	
							<input type="submit" name="unbookmark" value="Remove from my Locations"/>
							<?
						}
						?>
					</form>
					<?
				}

			?>
			</div>
		</div>	
		<?
	}


	public function renderFooterJs() {

		parent::renderFooterJs();
		?>
		<script type="text/javascript">	
			(function($){
				$(document).ready(function(){
					$('.toggle-btn').click(function(){
						$(this).next('.toggle-area').toggle();
					});

					$("#add-buoy-form").validate();
					$("#add-tide-station-form").validate();
					$("#report-form").validate();

				});
			})(jQuery);			
		</script>

		<?
		if($this->needPicup) {
			?>
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js"></script>
			<script type="text/javascript" src="<?=Path::toJs()?>lib/picup.js"></script>
			<script type="text/javascript">
				document.observe('dom:loaded', function(){
				//$(document).ready(function(){
					usePicup('<?=Path::toMobileImageProcess()?>', 'report_form');
				});
			</script>	
			<?	
		}
	}	
	


}