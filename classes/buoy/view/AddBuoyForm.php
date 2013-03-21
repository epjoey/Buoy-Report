<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


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
					<input type="submit" name="enterbuoy" value="Enter Buoy" />
					<input type="hidden" name="locationid" value="<?=$location->id?>" />
				</div>
			</form>
			<script type="text/javascript"> 
				(function(){
					new BR.LocationAddBuoyForm({
						el: '#add-buoy-form',
						existingStationsUrl: "<?=Path::toAjax()?>buoy/buoy-selector.php",
						locationId: "<?=$location->id?>"
					});				
				})()
			</script>	
		</div>	
		<?
	}
}
?>