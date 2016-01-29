<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class FormFields {
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

	public static function renderTimeSelect($location) {
		$localTimezone = new DateTimeZone($location->timezone);
		$now = new DateTime('now', $localTimezone)
		?>
		<div class="field time select-field required first">
			<label for="time_offset">Time:</label>
			<select name="time_offset" id="time-offset">
				<option value="0">Now</option>
				<?
				for ($i=1; $i <= 240; $i++) {
					$now->modify("-".(60 * 60)." seconds")
					?>
					<option value="-<?=$i?>">
						<?= $i . " hours ago" ?>
						<?= $i >= 24 ? "(" .  $now->format('m/d/y g:i A') . ")" : "" ?>
					</option>
					<?
				}
				?>
				<option value="older-date">Older...</option>
			</select>
			<input id="report-form-older-date" style="display:none" placeholder="mm/dd/yyyy hh:mm:ss" type="text" value="" name="time" />
			<script type="text/javascript">
				(function(){
					$('select#time-offset').change(function(event){
						if ($(this).find(":selected").val() == 'older-date') {
							$('#report-form-older-date').show();
						}
					});
				})();
			</script>
		</div>
		<?
	}

	public static function renderQualitySelect($currentQuality = null) {
		?>
		<div class="field quality radio-menu">
			<label for="quality">Session was:</label>
			<div class="radio-fields">
				<?
				foreach (ReportOptions::quality() as $key=>$value) {
					if ($currentQuality == $key) {
						$selected = "checked = 'true'";
					} else {
						$selected = '';
					}

					?>
					<span class="radio-field">
						<input type="radio" class="required" name="quality" id="quality-<?=$key?>" value="<?=$key?>" <?=$selected?> /><label for="quality-<?=$key?>"> <?=$value?></label>
					</span>
					<?
				}
				?>
			</div>
		</div>
		<?
	}

	public static function renderImageInput($currImagePath) {
		?>
		<span id='render-image-input' class="render-image-input">
			<label for="upload">Upload <?= $currImagePath ? "New" : "" ?> Image:&nbsp;</label>
			<?
			if ($currImagePath) {
				$image = getImageInfo($currImagePath, 200, 200);
				if ($image) {
					?>
					<div class="image-container">
						<a href="<?=$image['src']?>" target="_blank"><image src="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/></a>
						<label><input type="checkbox" name="delete-image" id="" value="true" /> Delete Image</label>
					</div>
					<?
				}
			}
			?>
			<input type="file" name="upload" capture="camera" />
			<input type="hidden" name="imageurl" value="" />
			<span class="loader dark"></span>
			<span class="field-value uploading">Preparing&nbsp;</span>
			<span class="field-value image-name"></span>
			<span class="remove-x">[x]</span>
		</span>
		<script type="text/javascript">
			(function($){
				new BR.UploadImageField({
					el: '#render-image-input'
				});
			})(jQuery);
		</script>
		<?
	}
}