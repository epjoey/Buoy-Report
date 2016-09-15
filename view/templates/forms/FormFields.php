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

	public static function renderTimeSelect($location, $options = array()) {
		$localTimezone = new DateTimeZone($location->timezone);
		$now = new DateTime('now', $localTimezone)
		?>
		<div class="field time select-field required first">
			<?
			if($options['showLabel']){
				?>
				<label for="time_offset">Time:</label>
				<?
			}
			?>
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
						} else {
							$('#report-form-older-date').hide();
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

				if (!window.FormData) {
					console && console.log('The File APIs are not fully supported in this browser.');
					return;
				}
				var $el = $('#render-image-input');
				var $input = $el.find("input[name='upload']");
				var $imageNameDisplay = $el.find('.image-name');
				var $imageUrlInput = $el.find("input[name='imageurl']");
				var $formSubmit = $el.parents('form').find("[type='submit']");
				var IMGUR_CLIENT_ID = 'edda62204c13785';				

				var removeFile = function(event){
					$el.removeClass('has-image').removeClass('is-loading');
					$imageUrlInput.val("");
					$formSubmit.attr('disabled', null);
					$imageNameDisplay.text("");
				};

				var handleFileSelect = function(event){
					console.log(event)
					var files = event.target.files;
					if (!files || !files.length) {
						return;
					}
					var file = files[0];
					var formData = new FormData();
					formData.append("image", file);
					$formSubmit.attr('disabled', 'disabled');
					$el.addClass('is-loading');
					$el.addClass('has-image');
					$input.val('');
					$imageNameDisplay.text(file.name);
					$.ajax({
						url: "https://api.imgur.com/3/image",
						type: "POST",
						datatype: "json",
						contentType: false,
						processData: false,
						headers: {
							'Authorization': 'Client-ID ' + IMGUR_CLIENT_ID
						},
						data: formData,
						success: function(result) { 
							$formSubmit.attr('disabled', null);
							$el.removeClass('is-loading');
							$imageUrlInput.val(result.data.link);
						},
						error: function() { console.log("error uploading image"); },
					});
				};
				
				$el.on("change", "[name='upload']", handleFileSelect);
				$el.on("click", ".remove-x", removeFile);				

			})(jQuery);
		</script>
		<?
	}
}