<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class AddTideStationForm {

	public function renderAddTideStationForm($addStationError = NULL) {
		?>		
		<div id="add-tide-station-div" class="form-container add-station-div" style="<?= isset($addStationError) ? '' : 'display:none;' ?> margin-top:12px">
			<form id="add-tide-station-form" action="" method="post">
				<p>Find nearby tide stations <a target="_blank" href="http://tidesonline.nos.noaa.gov/geographic.html">here</a> or <a href="javascript:" id="add-existing-tidestation">add existing station.</a></p>
				<span id="existing-tidestation-container" style="display:none" class="station-list ajax-load"></span>
				<span class="submission-error"><?= isset($addStationError) ? $addStationError : '';?></span>
				<div class="field">
					<label for="station-id">Station Number</label>
					<input type="text" class="text-input required" id="station-id" name="station-id" />
				</div>
				<div class="field">
					<label for="station-name">Station Detail (optional)</label>	
					<input type="text" class="text-input" id="station-name" name="station-name" />
				</div>
				<div class="field">			
					<input type="hidden" name="submit" value="enter-tide-station" />
					<input type="submit" name="enter-tide-station" value="Enter Tide Station" />
				</div>
			</form>	
		</div>
		<?
	}	
}
?>