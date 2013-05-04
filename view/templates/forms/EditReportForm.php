<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditReportForm {

	public static function renderEditReportForm($report, $options = array()) {
		$defaultOptions = array(
			'statusMsg' => '',
			'needPicup' => false
		);
		$options = array_merge($defaultOptions, $options);
		$statusMsg = $options['statusMsg'];
		$needPicup = $options['needPicup'];

		//remove when I make this joined with report query
		$locationInfo = $report['locationInfo'];

		if (isset($locationInfo['timezone'])) {
			$reportTime = getLocalTimeFromGMT($report['reportdate'], $locationInfo['timezone']);
			$obsTime = getLocalTimeFromGMT($report['obsdate'], $locationInfo['timezone']);
			$tzAbbrev = getTzAbbrev($locationInfo['timezone']);
		} else {
			$reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['reportdate']);
			$obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['obsdate']);
			$tzAbbrev = "GMT";						
		}

		?>
		<h1 class="form-head">Edit Report <?= $report['id'] ?></h1>
		<h4>
			<a class="loc-name" href="<?=Path::toLocation($report['locationid']);?>"><?= html($locationInfo['locname'])?></a> - 
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

				ReportFormFields::renderQualitySelect($report['quality']);

				if (!empty($locationInfo['sublocations'])) {
					ReportFormFields::renderSubLocationSelect($locationInfo['sublocations'], $report['sublocationid']);	
				}
				

				

				?>

				<div class="optional-fields">
					<? ReportFormFields::renderWaveHeightField(ReportOptions::getWaveHeights(), $report['waveheight']);?>

					<div class="field text">
						<label for="text">Report:</label>
						<textarea name="text" class="text-input" id="text"><?=$report['text']?></textarea>
					</div>	
					
					<? ReportFormFields::renderImageInput($report['imagepath'], $needPicup) ?>
				</div>

				<input type="hidden" name="id" id="id" value="<?=$report['id']?>" />
				<input type="hidden" name="imagepath" id="imagepath" value="<?=$report['imagepath']?>" />
				<input type="hidden" name="remoteImageURL" id="remoteImageURL" value="" />
				<input type="hidden" name="submit" value="update-report" />				
				<input type="submit" name="update_report" value="Update Report" />							
					
				
			</form>	
			<? self::renderDeleteReportForm($report); ?>
		</div>
		<?	
		if($needPicup) {
			?>
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js"></script>
			<script type="text/javascript" src="<?=Path::toJs()?>lib/picup.js"></script>
			<script type="text/javascript">
				document.observe('dom:loaded', function(){
				//$(document).ready(function(){
					usePicup('<?=Path::toMobileImageProcess()?>', 'report_form');
				});
			</script>	
			<?	
		}
	}

	public static function renderDeleteReportForm($report) {
		?>
		<form action="<?=Path::toHandleEditReportSubmission();?>" method="post" class="delete-form" id="delete-report-form">
			<input type="hidden" name="id" value="<?=$report['id']?>" />
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