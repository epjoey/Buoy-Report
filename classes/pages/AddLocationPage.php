<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/magicquotes.php';


class AddLocationPage extends GeneralPage {

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Submit Location';
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript" src="<?=Paths::toJs()?>timezone.js"></script>	
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#locationname').focus();
				$("#add-loc-form").validate();
			});
		</script>
		<?
	}	

	public function getBodyClassName() {
		return 'add-location-page';
	}	


	public function afterSubmit() {
		if(!$this->processSubmitForm()) {
			$this->loadData();
			$this->renderPage(); 
			exit();
		}
		header('Location:'.Paths::toLocation($this->newLocationId));
		exit();
	}

	public function renderBodyContent() {
		?>
			<h1 class="form-head">Submit New Location</h1>
			<div class="form-container">
				<form action="" method="post" id="add-loc-form" class="" >
					<? if (isset($this->submitError)) { ?>
						<span class="submission-error"><?= $this->submitError ?></span>
					<? } ?>			
					<div class="field">
						<label for="locationname">Location Name</label>
						<input id="locationname" class="text-input required" name="locationname" type="text" placeholder="Enter location" />
					</div>
					<div class="field">
						<input id="local-timezone" name="timezone" type="hidden" value="" />
						<input name="reporterid" type="hidden" value="<?=$this->userId?>" />
						<input name="submit-location" type="submit" value="Submit Location" />
					</div>
				</form>
			</div>
			<? //TODO:gotta get user timezone into TZ select ?>
			<script type="text/javascript">
				(function(){
					var timezone = jstz.determine_timezone();
					$('#local-timezone').val(timezone.name());
				})();
			</script>
		<?
	}
	
	public function processSubmitForm() {

		if (empty($_POST['locationname'])) {
			$this->submitError = 'Please enter a location';
			return FALSE;
		}
		
		if (Persistence::dbContainsLocation($_POST['locationname'])) {
			$this->submitError = $_POST['locationname'] . " already exists as an option";
			return FALSE;
		}

		//success
		if (!empty($_POST['timezone'])) {
			$timezone = $_POST['timezone'];
		} else {
			$timezone = 'UTC';
		}
		
		$newLocation = Persistence::insertLocation($_POST['locationname'], $timezone, $this->userId);
		
		$this->newLocationId = $newLocation;
		return TRUE;
	}
}
?>