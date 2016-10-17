<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class ReportForm {

	static function renderReportForm($location, $options = array()) {
		$defaultOptions = array(
			'statusMsg' => '',
		);
		$options = array_merge($defaultOptions, $options);
		$statusMsg = $options['statusMsg'];
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
				FormFields::renderTimeSelect($location, array('showLabel'=>true));

				if (!$location->tideStations && !$location->buoys) {

					?>
					<div class="field radio-menu include required">
						<div class="include-fields">
							<span>No buoys or tidestations assigned to location yet.</span>
						</div>
					</div>
					<?
				}

				FormFields::renderQualitySelect();
				?>
				<div class="optional-fields <?= $location->sublocations ? 'includes-sublocations' : ''?> ">
					<!--<h5 class="form-heading">Optional Fields</h5>-->
					<div class="fields">
						<?
						if ($location->sublocations) {
							FormFields::renderSubLocationSelect($location->sublocations);
						}

						FormFields::renderWaveHeightField(ReportOptions::getWaveHeights());?>

						<div class="field text">
							<label for="text">Report:</label>
							<textarea name="text" id="text" class="text-input" placeholder="how was it?" ></textarea>
						</div>

						<div class="field image last">
							<? FormFields::renderImageInput(null) ?>
						</div>
					</div>
				</div><!--end optional fields-->
				<input type="hidden" name="locationid" value="<?=$location->id?>" />
				<input type="hidden" name="locationname" value="<?=$location->locname?>" />
				<input type="hidden" name="submit" value="submit-report" />
				<input type="submit" name="submit_report" value="Submit Report" />
			</form>
		</div>
		<?
	}
}
?>