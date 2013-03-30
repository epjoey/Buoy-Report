<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class AddLocationPage extends Page {

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript" src="<?=Path::toJs()?>lib/timezone.js"></script>	
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

	public function renderBodyContent() {
		?>
			<h1 class="form-head">Submit New Location</h1>
			<div class="form-container">
				<form action="<?= Path::toPostLocation() ?>" method="post" id="add-loc-form" class="" >
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