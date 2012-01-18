<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LocationDetailPage.php';

class EditLocationPage extends LocationDetailPage {

	private $editLocationError = NULL;

	public function loadData() {
		parent::loadData();

		if (!Persistence::userCreatedLocation($this->userId, $this->locationId)) {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.Paths::toLocation($this->locationId));
			exit();	
		}

		$this->pageTitle = 'Edit: ' . $this->locInfo['locname'];

		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = "No Changes specified"; break;
				case 2: $e = "Location name specified already exists"; break;
				case 3: $this->addBouyError = "Please fill in bouy number"; break;
				case 4: $this->addStationError = "Please enter a station"; break;
			}
			$this->editLocationError = $e;
		}		
	}

	public function getBodyClassName() {
		return 'edit-location-page';
	}	
	
	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
			//	$("#edit-location-name-form").validate();
			});
		</script>
		<?
	}		

	public function afterSubmit() {
		if ($_POST['submit'] == 'update-name') {
			if (empty($_POST['locname']) || $_POST['locname'] == $this->locInfo['locname']) {
				$error = 1;
				header('Location:'.Paths::toEditLocation($this->locationId, $error));
				exit();				
			}
			if (Persistence::dbContainsLocation($_POST['locname'])) {
				$error = 2;
				header('Location:'.Paths::toEditLocation($this->locationId, $error));
				exit();		
			}
			Persistence::updateLocationName($this->locationId, $_POST['locname']);			
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}

		if ($_POST['submit'] == 'select-timezone') {
			if (empty($_POST['timezone']) || $_POST['timezone'] == $this->locInfo['timezone']) {
				$error = 1;
				header('Location:'.Paths::toEditLocation($this->locationId, $error));
				exit();				
			}			
			Persistence::updateLocationTimezone($this->locationId, $_POST['timezone']);	
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();	
		}		

		if ($_REQUEST['submit'] == 'enter-bouy') {
			if (empty($_POST['bouy-id'])) {
				$error = 3;
				header('Location:'.Paths::toLocation($this->locationId, $error));
				exit();	
			}

			if (!$this->isValidBouy($_POST['bouy-id'])) {
				$this->renderPage();
				exit();				
			}

			Persistence::insertBouy($_POST['bouy-id'], $_POST['bouy-name'], $this->locationId, $this->bouyCount + 1);
			header('Location:'.Paths::toEditLocation($this->locationId));
			exit();
		}	

		if ($_REQUEST['submit'] == 'enter-tide-station') {
			if (empty($_POST['station-id'])) {
				$error = 4;
				header('Location:'.Paths::toLocation($this->locationId, $error));
				exit();	
			}

			if (!$this->isValidTideStation($_POST['station-id'])) {
				$this->renderPage();
				exit();				
			}			
			
			Persistence::insertTideStation($_POST['station-id'], $_POST['station-name'], $this->locationId);
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
				<?
				if (isset($this->editLocationError)) {
					?>
					<span class="submission-error"><?= $this->editLocationError ?></span>
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
				

				if ($this->bouyCount < 3) {
					?>
					<p id="add-bouy-btn" class="edit-loc-link block-link">Add Bouy</p>
					<?
				}

				if (!isset($this->locInfo['tidestation'])) {
					?>
					<p id="add-tide-station-btn" class="edit-loc-link block-link">Add Tide Station</p>
					<?					
				}

				$this->renderAddStationContainers();

				/*
				if (isset($this->locInfo['forecast']))
				?>
				<form method="post" action="">
					<div class="field">
						<input type="text" name="forecast-link" class="text-input" value="<?=html($this->locInfo['forecast'])?>" />
						<input type="hidden" name="submit" value="forecast-link" />
						<input type="submit" name="update-name" value="Update Name" />
					</div>
				</form>	
				<? */ ?>			
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