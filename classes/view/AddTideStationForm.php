<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class AddTideStationForm {

	static function render($options=array()) {
		$defaultOptions = array(
			'location'=>null,
			'status'=>null,
			'defaultStation'=>null
		);
		$options = array_merge($defaultOptions, $options);
		$defaultStation = $options['defaultStation'];
		$status = $options['status'];
		$location = $options['location'];
		?>		

		<div id="add-tide-station-div" class="form-container add-station-div <?= isset($status) ? 'has-status' : '' ?>">
			<form id="add-tide-station-form" action="<?= Path::toLocationAddTidestation() ?>" method="post">
				
				<span class="submission-error"><?= isset($status) ? $status : '';?></span>

				<p>Find nearby tide stations 
					<a target="_blank" href="http://tidesonline.nos.noaa.gov/geographic.html">here</a> 
					or 
					<a href="javascript:" class="add-existing">add existing station.</a>
				</p>
				<div class="station-list ajax-load"></div>
				
				<div class="field">
					<label for="stationid">Station Number</label>
					<input type="text" class="text-input required station-id" name="stationid" />
				</div>
				<div class="field">
					<label for="stationname">Station Detail (optional)</label>	
					<input type="text" class="text-input" name="stationname" />
				</div>
				<div class="field">			
					<input type="submit" name="enter-tide-station" value="Enter Tide Station" />
					<input type="hidden" name="locationid" value="<?=$location->id?>" />
				</div>
			</form>	
			<script type="text/javascript"> 
				(function(){
					new BR.LocationAddBuoyForm({
						el: '#add-tide-station-form',
						existingStationsUrl: "<?= Path::toAjax()?>tide/tidestation-selector.php",
						locationId: "<?= $location->id ?>"
					});				
				})()
			</script>	
		</div>
		
		<?
	}	
}
?>