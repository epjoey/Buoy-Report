<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class AddBuoyForm {

	public function renderAddBuoyForm($addBuoyError = NULL, $showByDefult = FALSE, $defaults = array()) {

		?>
		<div id="add-buoy-div" class="form-container add-station-div" style="<?= isset($addBuoyError) || $showByDefult ? '' : 'display:none;' ?> margin-top:12px">
			<form id="add-buoy-form" action="" method="post">
				<p>Find nearby buoys <a target="_blank" href="http://www.ndbc.noaa.gov/rmd.shtml">here</a><span class="add-existing-buoy-link"> or <a href="javascript:" id="add-existing-buoy">add existing buoy.</a></span></p>
				<span id="existing-buoys-container" style="display:none" class="station-list ajax-load"></span>
				<span class="submission-error"><?= isset($addBuoyError) ? $addBuoyError : '';?></span>
				<div class="field">	
					<label for="buoy-id">Buoy Number </label>
					<input type="text" class="text-input required" id="buoy-id" name="buoy-id" <?
					 if (isset($defaults['buoyid'])) {
					 	print "value='" . $defaults['buoyid'] . "'";
					 } else {
					 	print "placeholder='Enter Buoy Number'";
					 }
					 ?> />
				</div>
				<div class="field">	
					<label for="buoy-name">Buoy Detail (optional)</label>
					<input type="text" class="text-input" id="buoy-name" name="buoy-name" <?
					 if (isset($defaults['name'])) {
					 	print "value='" . $defaults['name'] . "'";
					 } else {
					 	print "placeholder='location, coords, moored...'";
					 }
					 ?> />
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