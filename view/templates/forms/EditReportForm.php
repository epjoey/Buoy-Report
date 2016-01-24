<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditReportForm {

	public static function renderEditReportForm($report, $options = array()) {
		$defaultOptions = array(
			'statusMsg' => '',
		);
		$options = array_merge($defaultOptions, $options);
		$statusMsg = $options['statusMsg'];

		//remove when I make this joined with report query
		$location = $report->location;

		if (isset($location->timezone)) {
			$reportTime = getLocalTimeFromGMT($report->reportdate, $location->timezone);
			$obsTime = getLocalTimeFromGMT($report->obsdate, $location->timezone);
			$tzAbbrev = getTzAbbrev($location->timezone);
		} else {
			$reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report->reportdate);
			$obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $report->obsdate);
			$tzAbbrev = "GMT";						
		}

		?>
		<h1 class="form-head">Edit Report <?= $report->id ?></h1>
		<h4>
			<a class="loc-name" href="<?=Path::toLocation($report->locationid);?>"><?= html($location->locname)?></a> - 
			<span class="obs-time"><?=$obsTime?> <span class="tz">(<?=$tzAbbrev?>)</span></span>
		</h4>
		<div class="form-container report-form-container">
			<form action="<?=Path::toHandleEditReportSubmission();?>" method="POST" enctype="multipart/form-data" id="edit-report-form">
				
				<? 
				if (isset($statusMsg)) {
					?>
					<span class="submission-error"><?= $statusMsg ?></span>
					<? 
				}

				FormFields::renderQualitySelect($report->quality);
				if (!empty($location->sublocations)) {
					FormFields::renderSubLocationSelect($location->sublocations, $report->sublocationid);	
				}
				

				

				?>

				<div class="optional-fields">
					<? FormFields::renderWaveHeightField(ReportOptions::getWaveHeights(), $report->waveheight);?>

					<div class="field text">
						<label for="text">Report:</label>
						<textarea name="text" class="text-input" id="text"><?=$report->text?></textarea>
					</div>
					<div class="field image last">
						<? FormFields::renderImageInput($report->imagepath) ?>
					</div>
				</div>

				<input type="hidden" name="id" id="id" value="<?=$report->id?>" />
				<input type="hidden" name="submit" value="update-report" />				
				<input type="submit" name="update_report" value="Update Report" />							
					
				
			</form>	
			<? self::renderDeleteReportForm($report); ?>
		</div>
		<?
	}

	public static function renderDeleteReportForm($report) {
		?>
		<form action="<?=Path::toHandleEditReportSubmission();?>" method="post" class="delete-form" id="delete-report-form">
			<input type="hidden" name="id" value="<?=$report->id?>" />
			<input type="hidden" name="submit" value="delete-report" />
			<input type="button" id="delete-btn" class="delete-btn" value="Delete Report" />
			<div class="overlay" id="delete-btn-overlay" style="display:none;">
				<p>Are you sure you want to delete this report? <strong>All data will be lost!</strong></p>
				<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
				<input class="confirm" type="submit" name="delete-report" id="confirm-deletion" value="Confirm"/>
			</div>
		</form>

		<script>
			(function($) {
				$('#delete-btn').click(function(){
					$('#delete-btn-overlay').show();
					window.scrollTo(0,0);
				});

				$('#delete-btn-overlay #cancel-deletion').click(function(){
					$('#delete-btn-overlay').hide();
				});	
			})(jQuery);		
		</script>					
		

		<?
	}	


}

?>