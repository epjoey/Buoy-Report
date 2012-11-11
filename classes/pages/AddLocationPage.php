<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';


class AddLocationPage extends Page {

	private $addLocationError = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Submit Location';
		
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Please enter a location';; break;
				case 2: $e = "Location name specified already exists"; break;
			}
			$this->addLocationError = $e;
		}		
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript" src="<?=Path::toJs()?>timezone.js"></script>	
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

		if (empty($_POST['locationname'])) {
			$error = 1;
			header('Location:'.Path::toSubmitLocation($error));
			exit();
		}
		
		if (Persistence::dbContainsLocation($_POST['locationname'])) {
			$error = 2;
			header('Location:'.Path::toSubmitLocation($error));
			exit();		
		}

		//success
		if (!empty($_POST['timezone'])) {
			$timezone = $_POST['timezone'];
		} else {
			$timezone = 'UTC';
		}
		
		$newLocationId = Persistence::insertLocation($_POST['locationname'], $timezone, $this->user->id);
		header('Location:'.Path::toLocation($newLocationId));
		exit();
	}

	public function renderBodyContent() {
		?>
			<h1 class="form-head">Submit New Location</h1>
			<div class="form-container">
				<form action="" method="post" id="add-loc-form" class="" >
					<? if (isset($this->addLocationError)) { ?>
						<span class="submission-error"><?= $this->addLocationError ?></span>
					<? } ?>			
					<div class="field">
						<label for="locationname">Location Name</label>
						<input id="locationname" class="text-input required" name="locationname" type="text" placeholder="Enter location" />
					</div>
					<div class="field">
						<input id="local-timezone" name="timezone" type="hidden" value="" />
						<input name="reporterid" type="hidden" value="<?=$this->user->id?>" />
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

}
?>