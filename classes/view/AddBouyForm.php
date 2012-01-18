<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class AddBouyForm {

	public function renderAddBouyForm($addBouyError = NULL, $showByDefult = FALSE) {
		?>
		<div id="add-bouy-div" class="form-container add-station-div" style="<?= isset($addBouyError) || $showByDefult ? '' : 'display:none;' ?> margin-top:12px">
			<form id="add-bouy-form" action="" method="post">
				<p>Find nearby bouys <a target="_blank" href="http://www.ndbc.noaa.gov/rmd.shtml">here</a> or <a href="javascript:" id="add-existing-bouy">add existing bouy.</a></p>
				<span id="existing-bouys-container" style="display:none" class="station-list ajax-load"></span>
				<span class="submission-error"><?= isset($addBouyError) ? $addBouyError : '';?></span>
				<div class="field">	
					<label for="bouy-id">Bouy Number </label>
					<input type="text" class="text-input required" id="bouy-id" name="bouy-id" placeholder="Enter Bouy Number"/>
				</div>
				<div class="field">	
					<label for="bouy-name">Bouy Detail (optional)</label>
					<input type="text" class="text-input" id="bouy-name" name="bouy-name" placeholder="location, coords, moored..."/>
				</div>
				<div class="field">	
					<input type="hidden" name="submit" value="enter-bouy" />
					<input type="submit" name="enter-bouy" value="Enter Bouy" />
				</div>
			</form>
		</div>	
		<?
	}
}
?>