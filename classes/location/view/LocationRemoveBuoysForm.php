<?
class LocationRemoveBuoysForm {
	static function render($location) {
		?>
		<form action="<?=Path::toLocationRemoveBuoy()?>" method="post" class="location-remove-buoys">
			<input type="hidden" name="locationid" value="<?=$location->id?>"/>
			<?
			foreach($location->buoys as $buoy){
				?>
				<div class="buoy-to-remove">
					<span class="buoy">
						<a target="_blank" href="<?=Path::toNOAABuoy($buoy->buoyid)?>"><?= html($buoy->buoyid) ?></a>
						<?= html($buoy->name)?>
					</span>
					<input type="hidden" name="buoyid" value="<?=$buoy->buoyid?>"/>
					<input type="submit" name="remove-buoy" value="Remove" />
				</div>
				<?
			}
			?>
		</form>	
		<script type="text/javascript"> 
			(function(){
				new BR.LocationRemoveBuoysForm({
					el: $('.location-remove-buoys')
				});					
			})();
		</script>			
		<?
	}
}
?>