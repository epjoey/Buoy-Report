<?
class ReportFormFields {
	public static function renderWaveHeightField($waveHeights = array(), $preselected = null) {
		?>
		<div class="field wave-height select-field">
			<label for="quality">Wave Height: (backs)</label>
			<select name="waveheight" id="waveheight">
				<option value="" />select</option>
				<?
				foreach ($waveHeights as $key=>$value) {
					$selected = isset($preselected) && $preselected == $key ? "selected = 'selected'" : ''; 
					?>
					<option value="<?=$key?>" <?=$selected?> /><?=$value[0] . '-' . $value[1]?></option>
					<?
				}
				?>
			</select>
		</div>	
		<?
	}

	public static function renderSubLocationSelect($sublocations, $preselected = null, $options = array()) {
		?>
		<div class="field sublocations select-field">
			<label for="sublocationid">Sub-Spot:</label>
			<select name="sublocationid" id="sublocationid">
				<option value="" />select</option>
				<?
				foreach ($sublocations as $sublocation) {
					$selected = isset($preselected) && $preselected == $sublocation->sl_id ? "selected = 'selected'" : ''; 
					?>
					<option value="<?=$sublocation->sl_id?>" <?=$selected?> /><?=$sublocation->sl_name?></option>
					<?
				}
				?>
			</select>
		</div>	
		<?
	}

	public static function renderTimeSelect() {
		?>				
		<div class="field time select-field required">
			<label for="time_offset">Time:</label>
			<select name="time_offset" id="time-offset">
				<option value="0">Now</option>
				<?
				for ($i=1; $i <= 48; $i++) { 
					?>
					<option value="-<?=$i?>"><?= $i . " hours ago" ?></option>
					<?
				}

				?>
				
				<? //<option value="" id="arbitrary-date-trigger">Enter Arbitrary Date</option> ?>
			</select>
			<?
			/*
			<input id="report-form-arbitrary-date-field" style="display:none" type="text" value="" name="arbitrary_date" />
			<script type="text/javascript">
				(function(){
					$('select#time-offset').change(function(){
						if ($(this).find("option:selected").attr("id") == 'arbitrary-date-trigger') {
							$('#report-form-arbitrary-date-field').show();	
						}
					});
				})();
			</script>
			*/
			?>
		</div>
		<?
	}	
}