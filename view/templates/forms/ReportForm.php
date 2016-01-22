<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class ReportForm {

	static function renderReportForm($location, $options = array()) {
		$defaultOptions = array(
			'statusMsg' => '',
			'needPicup' => false
		);
		$options = array_merge($defaultOptions, $options);
		$statusMsg = $options['statusMsg'];
		$needPicup = $options['needPicup'];
		$user = UserService::getUser();
		?>
		<div class="form-container report-form-container">
			<form id="report-form" action="<?=Path::toHandleReportSubmission()?>" method="post" >
				<?
				if ($statusMsg) {
					?>
					<span class="submission-error"><?= $statusMsg ?></span>
					<?
				}
				ReportFormFields::renderTimeSelect($location);

				?>


				<div class="field radio-menu include required">
					<label for="include">Include:</label>
					<div class="include-fields">
						<?
						/* use js to get values of attributes on location selector */
						foreach ($location->buoys as $buoy) {
							?>
							<span class="radio-field">
								<input type="checkbox" id="buoy-<?=$buoy->buoyid ?>" name="buoys[]" value="<?=$buoy->buoyid ?>" checked='checked' />
								<label for="buoy-<?=$buoy->buoyid ?>">Bouy <?=$buoy->buoyid ?></label>
							</span>
							<?
						}
						foreach ($location->tideStations as $ts) {
							?>
							<span class="radio-field">
								<input type="checkbox" id="ts-<?=$ts->stationid ?>" name="tidestations[]" value="<?=$ts->stationid ?>" checked='checked' />
								<label for="ts-<?=$ts->stationid ?>">Tide Station <?=$ts->stationid ?></label>
							</span>
							<?
						}
						if (!$location->tideStations && !$location->buoys) {
							?>
							<span>No buoys or tidestations assigned to location yet.</span>
							<?
						}
						?>
					</div>
				</div>
				<?
				ReportFormFields::renderQualitySelect();
				?>
				<div class="optional-fields <?= $location->sublocations ? 'includes-sublocations' : ''?> ">
					<!--<h5 class="form-heading">Optional Fields</h5>-->
					<div class="fields">
						<?
						if ($location->sublocations) {
							ReportFormFields::renderSubLocationSelect($location->sublocations);
						}

						ReportFormFields::renderWaveHeightField(ReportOptions::getWaveHeights());?>

						<div class="field text">
							<label for="text">Report:</label>
							<textarea name="text" id="text" class="text-input" placeholder="how was it?" ></textarea>
						</div>

						<div class="field image last">
							<label for="upload">Upload an image:</label>
							<input type="file" name="upload" capture="camera">
						</div>
					</div>
				</div><!--end optional fields-->
				<input type="hidden" name="remoteImageURL" id="remoteImageURL" value="" />
				<input type="hidden" name="locationid" value="<?=$location->id?>" />
				<input type="hidden" name="locationname" value="<?=$location->locname?>" />
				<input type="hidden" name="submit" value="submit-report" />
				<input type="submit" name="submit_report" value="Submit Report" />
			</form>
		</div>
		<script type="text/javascript">
			var uploadInput = $("input[name='upload']");
			var handleFileSelect = function(evt) {
				var files = evt.target.files;
				if (files && files.length) {
					var formData = new FormData();
					formData.append("image", uploadInput[0].files[0]);
					uploadInput.val('');
					$.ajax({
						url: "https://api.imgur.com/3/image",
						type: "POST",
						datatype: "json",
						contentType: false,
						processData: false,
						headers: {
							'Authorization': 'Client-ID edda62204c13785'
						},
						data: formData,
						success: function(result) { 
							var link = result.data.link;
							$("input[name='remoteImageURL']").val(link);
						},
						error: function() { console.log("ERRORZ"); },
					});
				}
			};
			if (window.FormData) {
				uploadInput.on('change', handleFileSelect);
			} else {
				console && console.log('The File APIs are not fully supported in this browser.');
			}
		</script>
		<?
	}
}
?>