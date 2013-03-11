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
		<div id="add-buoy-div" class="form-container add-station-div" style="<?= isset($status) ? 'has-status' : '' ?> margin-top:12px">
			<form id="add-buoy-form" action="<?= Path::toLocationAddBuoy()?>" method="post">
				<p>Find nearby buoys 
					<a target="_blank" href="http://www.ndbc.noaa.gov/rmd.shtml">here</a> 
					or 
					<a href="javascript:" id="add-existing-buoy">add existing buoy.</a>
				</p>
				<div id="existing-buoys-container" style="display:none" class="station-list ajax-load"></div>			
			
				<span class="submission-error"><?= isset($status) ? $status : '';?></span>
				<div class="field">	
					<label for="buoy-id">Buoy Number </label>
					<input type="text" class="text-input required" id="buoy-id" name="buoy-id" placeholder='Enter Buoy Number' value="<?= isset($defaultBuoy) ? $defaultBuoy->buoyid : ''?>" />
				</div>
				<div class="field">	
					<label for="buoy-name">Buoy Detail (optional)</label>
					<input type="text" class="text-input" id="buoy-name" placeholder='location, coords, moored...' name="buoy-name" value="<?= isset($defaultBuoy->name) ? $defaultBuoy->name : '' ?>"/>
				</div>
				<div class="field">	
					<input type="submit" name="enter-buoy" id="enter-buoy" value="Enter Buoy" />
				</div>
				<input type="hidden" name="location-id" value="<?=$location->id?>" />
			</form>
		</div>	
		<script type="text/javascript"> 
			(function(){
				new BR.AddBuoyToLocationForm({
					el: '#add-buoy-div'
					// trigger: '#add-existing-buoy',
					// container: '#existing-buoys-container',
					// selectorUrl: "<?=Path::toAjax()?>buoy/buoy-selector.php?locationid=<?=$location->id?>",
					// addBuoyUrl: "<?=Path::toLocationAddBuoy()?>",
					// locationId: "<?=$location->id?>"
				});					
			})()
		</script>		
		<?
	}
}
?>