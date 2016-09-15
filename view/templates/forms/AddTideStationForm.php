<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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

			<form id="remove-tide-station-form" class="remove-station-form" action="<?= Path::toLocationRemoveTidestation() ?>" method="post">
				<input type="hidden" name="locationid" value="<?=$location->id?>"/>
				<?
				foreach($location->tideStations as $tideStation) {
					?>
					<div class="input-field">
						<a target="_blank" href="<?=Path::toNOAATideStation($tideStation->stationid)?>"><?=$tideStation->stationid?></a> <?= $tideStation->stationname ?>
						<input type="checkbox" name="stationid" value="<?= $tideStation->stationid ?>"/>
						<span class="button submit">X</span>
					</div>
					<?
				}
				?>
				<input type="submit" name="remove-station" value="Remove Selected Stations"/>
			</form>

			<form id="add-tide-station-form" class="labels-left" action="<?= Path::toLocationAddTidestation() ?>" method="post">
				
				<span class="submission-error"><?= isset($status) ? $status : '';?></span>

				<p>Find nearby tide stations 
					<a target="_blank" href="http://tidesonline.nos.noaa.gov/geographic.html">here</a> 
					or 
					<a href="javascript:" class="add-existing">add existing station.</a>
				</p>
				<div class="station-list ajax-load"></div>
				
				<div class="field">
					<label for="stationid">Station Number</label>
					<input type="text" class="text-input required station-id" name="stationid" placeholder='Enter Station Number' value="<?= isset($defaultStation) ? $defaultStation->stationid : ''?>" />
				</div>
				<div class="field">
					<label for="stationname">Station Detail</label>	
					<input type="text" class="text-input required station-name" name="stationname" placeholder='location, coords...' value="<?= isset($defaultStation) ? $defaultStation->stationname : ''?>"/>
				</div>
				<div class="field">			
					<input type="submit" name="enter-tide-station" value="Add Tide Station" />
					<input type="hidden" name="locationid" value="<?=$location->id?>" />
				</div>
			</form>	
			<script type="text/javascript"> 
				(function($){
					new BR.LocationRemoveStationForm({
						el: '#remove-tide-station-form',
						locationId: "<?=$location->id?>"
					});					
					new BR.LocationAddStationForm({
						el: '#add-tide-station-form',
						existingStationsUrl: "/controllers/tide/tidestation-selector.php",
						locationId: "<?= $location->id ?>"
					});				
				})(jQuery)
			</script>	
		</div>
		
		<?
	}	
}
?>