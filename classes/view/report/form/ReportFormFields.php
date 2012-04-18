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

	public static function renderSubLocationSelect($sublocations, $preselected = null) {
		?>
		<div class="field sublocations select-field">
			<label for="sublocation">Sub-Spot:</label>
			<select name="sublocation" id="sublocation">
				<option value="" />select</option>
				<?
				foreach ($sublocations as $sublocation) {
					$selected = isset($preselected) && $preselected == $key ? "selected = 'selected'" : ''; 
					?>
					<option value="<?=$sublocation->sl_id?>" <?=$selected?> /><?=$sublocation->sl_name?></option>
					<?
				}
				?>
			</select>
		</div>	
		<?
	}	
}