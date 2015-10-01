<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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

	public static function renderImageInput($currImagePath, $needPicup) {
		?>
		<div class="field image last">
			<label for="upload">Upload <?= $currImagePath ? "New" : "" ?> Image:</label>

			<?
			if ($currImagePath) {
				$image = getImageInfo($currImagePath, 200, 200);
				if ($image) {
					?>
					<span class="image-container">
						<a href="<?=$image['src']?>" target="_blank"><image src="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/></a>
						<label><input type="checkbox" name="delete-image" id="" value="true" /> Delete Image</label>
					</span>
					<?
				}
			}
			?>

			<input type="file" name="upload" id="upload" capture="camera">
			<span id="mobile-image-name" class="mobile-note">
				<?
				if($needPicup) {
					?>
					You will need <a href="itms-apps://itunes.com/apps/picup" target="_blank">Picup</a> to upload photos from your phone.
					<?
				}
				?>
			</span>
		</div>
		<?
	}
}