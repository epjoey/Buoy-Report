<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class AddBuoyForm {

	static function render($options=array()) {
		$defaultOptions = array(
			'location'=>null,
			'status'=>null,
			'defaultBuoy'=>null
		);
		$options = array_merge($defaultOptions, $options);
		$defaultBuoy = $options['defaultBuoy'];
		$status = $options['status'];
		$location = $options['location'];
		?>

		<div id="add-buoy-div" class="form-container add-station-div <?= isset($status) ? 'has-status' : '' ?>">


			<form id="remove-buoy-form" class="remove-station-form" action="<?= Path::toLocationRemoveBuoy() ?>" method="post">
				<input type="hidden" name="locationid" value="<?=$location->id?>"/>
				<?
				foreach($location->buoys as $buoy) {
					?>
					<div class="input-field">
						<a class="buoy-iframe-link" target="_blank" href="<?=Path::toNOAABuoy($buoy->buoyid)?>"><?= $buoy->buoyid ?></a> <?= $buoy->name ?>
						<input type="checkbox" name="buoyid" value="<?= $buoy->buoyid ?>"/>
						<span class="button submit">X</span>
					</div>
					<?
				}
				?>
				<input type="submit" name="remove-station" value="Remove Selected Buoys"/>
			</form>
			

			<form id="add-buoy-form" action="<?= Path::toLocationAddBuoy()?>" method="post">
				
				<span class="submission-error"><?= isset($status) ? $status : '';?></span>

				<p>Find nearby buoys 
					<a target="_blank" href="http://www.ndbc.noaa.gov/rmd.shtml">here</a> 
					or 
					<a href="javascript:" class="add-existing">add existing buoy.</a>
				</p>
				<div class="station-list ajax-load"></div>			
			
				<div class="field">	
					<label for="stationid">Buoy Number </label>
					<input type="text" class="text-input required station-id" name="buoyid" placeholder='Enter Buoy Number' value="<?= isset($defaultBuoy) ? $defaultBuoy->buoyid : ''?>" />
				</div>
				<div class="field">	
					<label for="buoy-name">Buoy Detail</label>
					<input type="text" class="text-input required station-name" placeholder='location, coords, moored...' name="buoyname" value="<?= isset($defaultBuoy->name) ? $defaultBuoy->name : '' ?>"/>
				</div>
				<div class="field">	
					<input type="submit" name="enterbuoy" value="Add Buoy" />
					<input type="hidden" name="locationid" value="<?=$location->id?>" />
				</div>
			</form>
			<script type="text/javascript"> 
				(function($){
					new BR.LocationRemoveStationForm({
						el: '#remove-buoy-form',
						locationId: "<?=$location->id?>"
					});
					new BR.LocationAddStationForm({
						el: '#add-buoy-form',
						existingStationsUrl: "/api/buoy/buoy-selector.php",
						locationId: "<?=$location->id?>"
					});				
				})(jQuery)
			</script>	
		</div>	
		<?
	}
}
?>