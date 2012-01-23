<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class AddBuoyForm {

	public function renderAddBuoyForm($addBuoyError = NULL, $showByDefult = FALSE) {
		?>
		<div id="add-buoy-div" class="form-container add-station-div" style="<?= isset($addBuoyError) || $showByDefult ? '' : 'display:none;' ?> margin-top:12px">
			<form id="add-buoy-form" action="" method="post">
				<p>Find nearby buoys <a target="_blank" href="http://www.ndbc.noaa.gov/rmd.shtml">here</a> or <a href="javascript:" id="add-existing-buoy">add existing buoy.</a></p>
				<span id="existing-buoys-container" style="display:none" class="station-list ajax-load"></span>
				<span class="submission-error"><?= isset($addBuoyError) ? $addBuoyError : '';?></span>
				<div class="field">	
					<label for="buoy-id">Buoy Number </label>
					<input type="text" class="text-input required" id="buoy-id" name="buoy-id" placeholder="Enter Buoy Number"/>
				</div>
				<div class="field">	
					<label for="buoy-name">Buoy Detail (optional)</label>
					<input type="text" class="text-input" id="buoy-name" name="buoy-name" placeholder="location, coords, moored..."/>
				</div>
				<div class="field">	
					<input type="hidden" name="submit" value="enter-buoy" />
					<input type="submit" name="enter-buoy" value="Enter Buoy" />
				</div>
			</form>
		</div>	
		<?
	}
}
?>