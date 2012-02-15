<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LocationDetailPage.php';

class EditLocationPage extends LocationDetailPage {

	private $editLocationError = NULL;

	public function loadData() {
		parent::loadData();

		$this->pageTitle = 'Edit: ' . $this->locInfo['locname'];
		
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 3: $e = "No Changes specified"; break;
				case 4: $e = "Location name specified already exists"; break;
			}
			$this->editLocationError = $e;
		}
	}

	public function getBodyClassName() {
		return 'edit-location-page';
	}	
	
	public function renderJs() {
		parent::renderJs();
	}		

	public function renderForbiddenMessage(){
		?>
			<p class="forbidden-message overlay">
				You must be the creator of the location to edit the location. However, you can still add forecast links, bouys, and tidestation (if the creator hasn't done so yet). <a href="<?=Path::toLocation($this->locationId);?>">Return to Location</a>
			</p>
		<?
	}

	public function afterSubmit() {

		$this->handleStationSubmission('toEditLocation');

		if ($_POST['submit'] == 'update-name') {
			if (empty($_POST['locname']) || $_POST['locname'] == $this->locInfo['locname']) {
				$error = 3;
				header('Location:'.Path::toEditLocation($this->locationId, $error));
				exit();				
			}
			if (Persistence::dbContainsLocation($_POST['locname'])) {
				$error = 4;
				header('Location:'.Path::toEditLocation($this->locationId, $error));
				exit();		
			}
			Persistence::updateLocationName($this->locationId, $_POST['locname']);			
			header('Location:'.Path::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'select-timezone') {
			if (empty($_POST['timezone']) || $_POST['timezone'] == $this->locInfo['timezone']) {
				$error = 3;
				header('Location:'.Path::toEditLocation($this->locationId, $error));
				exit();				
			}			
			Persistence::updateLocationTimezone($this->locationId, $_POST['timezone']);	
			header('Location:'.Path::toEditLocation($this->locationId));
			exit();	
		}			

		if ($_POST['submit'] == 'delete-location') {
			Persistence::deleteLocation($this->locationId);
			header('Location:'.Path::toUserHome());
			exit();	
		}

	}

	public function renderBodyContent() {

		if ($this->locInfo['creator'] != $this->user->id) {
			$this->renderForbiddenMessage();
		}
		else {
			$this->renderForm();		
		}		
		
	}

	public function renderForm() {
		?>
			<h2>
				<a href="<?=Path::toLocation($this->locationId)?>"><?= html($this->locInfo['locname'])?></a> > Edit
			</h2>
			<div class="form-container">
				<?
				if (isset($this->editLocationError)) {
					?>
					<span class="submission-error"><?= html($this->editLocationError) ?></span>
					<?
				}
				?>						
				<form method="post" action="" id="edit-location-name-form">
					<div class="field">
						<input type="text" name="locname" class="text-input required" value="<?=html($this->locInfo['locname'])?>" />
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
				<ul class="buoys">
				<? 
				if ($this->buoyCount > 0) {

					foreach($this->buoys as $key=>$buoy){
						
						$buoyInfo = Persistence::getBuoyInfo($buoy);
						?>
						<li class="field buoy">
							<label>Buoy: </label>
							<a target="_blank" href="http://www.ndbc.noaa.gov/station_page.php?station=<?=html($buoy)?>"><?= html($buoy) ?></a>
							<? 
							if (isset($buoyInfo['name'])) { 
								?>
								<span>(<?= html($buoyInfo['name'])?>)</span>
								<? 
							} 											
							?>
							<form action="" method="post" class="remove-buoy">
								<input type="hidden" name="submit" value="remove-buoy" />
								<input type="hidden" name="key" value="<?=$key?>"/>
								<input type="submit" name="remove-buoy" value="Remove" />
							</form>					
						</li>								
					<?
					}
				}
				if (isset($this->locInfo['tidestation'])) {
					$stationInfo = Persistence::getTideStationInfo($this->locInfo['tidestation']);
					?>
					<li class="field buoy">
						<label>Tide Station: </label>
						<a target="_blank" href="http://tidesonline.noaa.gov/plotcomp.shtml?station_info=<?=$this->locInfo['tidestation']?>"><?=$this->locInfo['tidestation']?></a>
						<? if(isset($stationInfo['stationname'])) { ?>
							<span> (<?= $stationInfo['stationname'] ?>)</span>
						<? } ?>
					
						<form action="" method="post" class="remove-buoy">
							<input type="hidden" name="submit" value="remove-tide-station" />
							<input type="hidden" name="tidestation" value="<?=$this->locInfo['tidestation']?>"/>
							<input type="submit" name="remove-tide-station" value="Remove" />
						</form>
					</li>
					<?
				} 						
				?>	
				</ul>
				<div class="add-station-section">
					<?
					if ($this->buoyCount < 3) {
						?>
						<p id="add-buoy-btn" class="edit-loc-link block-link">Add Buoy</p>
						<?
					}

					if (!isset($this->locInfo['tidestation'])) {
						?>
						<p id="add-tide-station-btn" class="edit-loc-link block-link">Add Tide Station</p>
						<?					
					}

					$this->renderAddStationContainers('toEditLocation');

				?>
				</div>			
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
						window.scrollTo(0,0);
					});

					$('#delete-btn-overlay #cancel-deletion').click(function(){
						$('#delete-btn-overlay').hide();
					});				
				</script>

			</div>
		<?
	}

	


}