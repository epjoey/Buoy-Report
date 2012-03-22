<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';

class ReportFormFields {
	public static function renderWaveHeightField($preselected = null) {
		?>
		<div class="field wave-height radio-menu">
			<label for="quality">Wave Height: (backs)</label>
			<?
			foreach (ReportOptions::waveHeight() as $key=>$value) {
				$selected = isset($preselected) && $preselected == $key ? "checked = 'true'" : ''; 
				?>
				<span class="radio-field">
					<input type="radio" name="waveheight" id="waveheight-<?=$value[0]?>" value="<?=$key?>" <?=$selected?> /><label for="waveheight-<?=$value[0]?>"> <?=$value[0] . '-' . $value[1]?></label>
				</span>
				<?
			}
			?>
		</div>	
		<?
	}
}