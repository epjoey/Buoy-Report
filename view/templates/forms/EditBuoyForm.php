<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditBuoyForm {

	static function render($options=array()) {
		$defaultOptions = array(
			'status'=>null,
			'buoy'=>null
		);
		$options = array_merge($defaultOptions, $options);
		$buoy = $options['buoy'];
		$status = $options['status'];
		?>

		<div class="form-container <?= isset($status) ? 'has-status' : '' ?>">
			<form id="edit-buoy-form" action="<?= Path::toEditBuoy()?>" method="post">
				
				<span class="submission-error"><?= isset($status) ? $status : '';?></span>
				<div class="field">	
					<label for="buoy-name">Buoy Detail</label>
					<input type="text" class="text-input required station-name" placeholder='location, coords, moored...' name="buoyname" value="<?= $buoy->name ?>"/>
				</div>
				<div class="field">	
					<input type="submit" name="enterbuoy" value="Edit Buoy" />
					<input type="hidden" name="buoyid" value="<?=$buoy->buoyid?>" />
				</div>
			</form>
		</div>	
		<?
	}
}
?>