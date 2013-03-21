<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';


class DeleteBuoyForm {

	static function render($options=array()) {
		$defaultOptions = array(
			'buoy'=>null
		);
		$options = array_merge($defaultOptions, $options);
		$buoy = $options['buoy'];
		?>

		<form action="<?=Path::toDeleteBuoy()?>" method="post" class="delete-form" id="delete-buoy-form">
			<input type="button" id="delete-buoy-btn" class="delete-btn" value="Delete Buoy" />
			
			<div class="overlay" id="delete-btn-overlay" style="display:none;">
				<p>Are you sure you want to delete this Buoy?</p>
				
				<input type="hidden" name="buoyid" value="<?= $buoy->buoyid?>"/>
				<input type="hidden" name="delete" value="true"/>
				
				<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
				<input class="confirm" type="submit" name="delete-buoy" id="confirm-deletion" value="Confirm"/>
			</div>
		
		</form>

		<script>
			$('#delete-buoy-btn').click(function(event){
				event.preventDefault();
				$('#delete-btn-overlay').show();
				window.scrollTo(0,0);
			});
			$('#delete-btn-overlay #cancel-deletion').click(function(){
				$('#delete-btn-overlay').hide();
			});				
		</script>			
		<?
	}
}
?>