<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LocationDetailPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/magicquotes.php';


class EditLocationPage extends LocationDetailPage {

	public function loadData() {
		parent::loadData();

		if (!Persistence::userCreatedLocation($this->userId, $this->locationId)) {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.Paths::toLocation($this->locationId));
			exit();	
		}

		$this->pageTitle = 'Edit: ' . $this->locInfo['locname'];
	}

	public function getBodyClassName() {
		return 'edit-location-page';
	}		

	public function afterSubmit() {
		if ($_POST['submit'] == 'update-name') {
			if (!empty($_POST['locname']) && $_POST['locname'] != $this->locInfo['locname']) {
				Persistence::updateLocationName($this->locationId, $_POST['locname']);			
			}
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'make-primary') {
			Persistence::setPrimaryBouy($this->locationId, $_POST['bouy']);		
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'enter-bouy') {

			//make this one javascript
			if (empty($_POST['bouy-id'])) {
				$this->addBouyError = "Please fill in bouy number";
				$this->renderPage();
				exit();
			}

			if (in_array($_POST['bouy-id'], $this->bouys)) {
				$this->addBouyError = "Bouy already assigned to location";
				$this->renderPage();
				exit();				
			}

			//dont need time because were just checking if data file exists/is online		
			$bouy = new BouyData($_POST['bouy-id'], 0);
			if (!$bouy->bouyExists) {
				$this->addBouyError = "Bouy " . $_POST['bouy-id'] . " cannot be reached";
				$this->renderPage();
				exit();
			}
			Persistence::insertBouy($_POST['bouy-id'], $_POST['bouy-name'], $this->locationId, $this->bouyCount + 1);
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'remove-bouy') {
			Persistence::removeBouyFromLocation($_POST['key'], $this->bouys, $this->locationId);
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();			
		}

		if ($_POST['submit'] == 'remove-tide-station') {
			Persistence::removeTideStationFromLocation($_POST['tidestation'], $this->locationId);
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();			
		}		
			
		if ($_POST['submit'] == 'enter-tide-station') {

			//make this one js
			if (empty($_POST['station-id'])) {
				$this->addStationError = "Please enter a station";
				$this->renderPage();
				exit();
			}
			
			//dont need time because were just checking if data file exists/is online		
			$tide = new TideData($_POST['station-id'], 0);
			if (!$tide->stationExists) {
				$this->addStationError = "Station " . $_POST['station-id'] . " cannot be reached";
				$this->renderPage();
				exit();
			}
			Persistence::insertTideStation($_POST['station-id'], $_POST['station-name'], $this->locationId);
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}
		
		if ($_POST['submit'] == 'select-timezone') {
			if (!empty($_POST['timezone']) && $_POST['timezone'] != $this->locInfo['timezone']) {
				Persistence::updateLocationTimezone($this->locationId, $_POST['timezone']);	
			}
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'delete-location') {
			Persistence::deleteLocation($this->locationId);
			header('Location:'.Paths::toUserHome());
			exit();	
		}

	}

	public function renderBodyContent() {
		$this->renderLocDetails();	
	}

	public function renderLocDetails() {
		?>
			<h2>
				<a href="<?=Paths::toLocation($this->locationId)?>"><?= html($this->locInfo['locname'])?></a> > Edit
			</h2>
			<div class="form-container">
				<form method="post" action="">
					<div class="field">
						<input type="text" name="locname" class="text-input" value="<?=html($this->locInfo['locname'])?>" />
						<input type="hidden" name="submit" value="update-name" />
						<input type="submit" name="update-name" value="Update Name" />
					</div>
				</form>
				<form action="" method="post">
					<? $zones = timezone_identifiers_list(); ?>
					<div class="field">
						<select name="timezone">
						<?
							if (isset($this->locInfo['timezone'])) {
								?>
									<option value="<?= $this->locInfo['timezone'] ?>" selected="selected"><?= $this->locInfo['timezone'] ?> (<?= getOffset($this->locInfo['timezone'])/3600; ?>)</option>
								<?
							}	
							foreach ($zones as $tz) {
								$offset = getOffset($tz)/3600;
								?>
									<option value="<?= $tz ?>"><?= $tz ?> (<?= $offset ?>)</option>			
								<?
							}
						?>
						</select>
						<input type="hidden" name="submit" value="select-timezone" />
						<input type="submit" name="select-timezone" value="Update Timezone" />
					</div>
				</form>
				<? 
				if ($this->bouyCount > 0) {
					?>
					<ul class="bouys">
					<?
					foreach($this->bouys as $key=>$bouy){
						
						$bouyInfo = Persistence::getBouyInfo($bouy);
						?>
						<li class="field bouy">
							<label>Bouy: </label>
							<a target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=html($bouy)?>"><?= html($bouy) ?></a>
							<? 
							if (isset($bouyInfo['name'])) { 
								?>
								<span>(<?= html($bouyInfo['name'])?>)</span>
								<? 
							} 											
							?>
							<form action="" method="post" class="remove-bouy">
								<input type="hidden" name="submit" value="remove-bouy" />
								<input type="hidden" name="key" value="<?=$key?>"/>
								<input type="submit" name="remove-bouy" value="Remove" />
							</form>					
						</li>								
						<?
						}
						if (isset($this->locInfo['tidestation'])) {

							$stationInfo = Persistence::getTideStationInfo($this->locInfo['tidestation']);
							?>
							<li class="field bouy">
								<label>Tide Station: </label>
								<a target="_blank" href="http://tidesonline.noaa.gov/plotcomp.shtml?station_info=<?=$this->locInfo['tidestation']?>"><?=$this->locInfo['tidestation']?></a>
								<? if(isset($stationInfo['stationname'])) { ?>
									<span> (<?= $stationInfo['stationname'] ?>)</span>
								<? } ?>
							
								<form action="" method="post" class="remove-bouy">
									<input type="hidden" name="submit" value="remove-tide-station" />
									<input type="hidden" name="tidestation" value="<?=$this->locInfo['tidestation']?>"/>
									<input type="submit" name="remove-tide-station" value="Remove" />
								</form>
							</li>
							<?
						} 						

					?>
					</ul>
					<?
				}
				
				if ($this->bouyCount < 3 && $this->userIsLoggedIn) {
					$form = new AddBouyForm;
					$form -> renderAddBouyForm($this->addBouyError);
				}

				if (!isset($this->locInfo['tidestation']) && $this->userIsLoggedIn) {
					$form = new AddTideStationForm;
					$form -> renderAddTideStationForm($this->addStationError);
				}

				?>
				<form action="" method="post" class="delete-form" id="delete-location-form">
					<input type="hidden" name="submit" value="delete-location" />
					<input type="button" id="delete-location-btn" class="delete-btn" value="Delete Location" />
					<div class="overlay" id="delete-btn-overlay" style="display:none;">
						<p>Are you sure you want to delete this location? <strong>All reports will be deleted forever!</strong></p>
						<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
						<input class="confirm" type="submit" name="delete-location" id="confirm-deletion" value="Confirm"/>
					</div>
				</form>

				<script>
					$('#delete-location-btn').click(function(){
						$('#delete-btn-overlay').show();
					});

					$('#delete-btn-overlay #cancel-deletion').click(function(){
						$('#delete-btn-overlay').hide();
					});				
				</script>

			</div>
		<?
	}

	


}