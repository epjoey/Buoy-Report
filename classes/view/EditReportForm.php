<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/SimpleImage.php';


class EditReportForm {
	private $report;
	private $locationInfo;

	public function loadData($report, $locationInfo = NULL) {
		
		$this->report = $report;

		if (!isset($locationInfo)) {
			$this->locationInfo = Persistence::getLocationInfoById($report['locationid']);			
		} else {
			$this->locationInfo = $locationInfo;
		}

		if (isset($this->locationInfo['timezone'])) {
			$this->reportTime = getLocalTimeFromGMT($report['reportdate'], $locationInfo['timezone']);
			$this->obsTime = getLocalTimeFromGMT($report['obsdate'], $locationInfo['timezone']);
			$this->tzAbbrev = getTzAbbrev($locationInfo['timezone']);
		} else {
			$this->reportTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['reportdate']);
			$this->obsTime = gmstrftime("%m/%d/%Y %l:%M %p", $report['obsdate']);
			$this->tzAbbrev = "GMT";						
		}

		if (isset($_GET['error']) && $_GET['error']) {
			$this->submitError = $_GET['error'];
		}		

	}

	public function renderEditReportForm($submitError = NULL, $isMobile) {	
		?>
		<h1 class="form-head">Edit Report <?= $this->report['id'] ?></h1>
		<h4>
			<a class="loc-name" href="<?=Paths::toLocation($this->report['locationid']);?>"><?= html($this->locationInfo['locname'])?></a> - 
			<span class="obs-time"><?=$this->obsTime?> <span class="tz">(<?=$this->tzAbbrev?>)</span></span>
		</h4>
		<div class="form-container">
			<form action="" method="POST" enctype="multipart/form-data" id="edit-report-form">
				
				<? if (isset($submitError)) {
					if ($submitError == 'upload-file') {
						$errorText = 'Error uploading file';
					} else if ($submitError == 'file-type') {
						$errorText = 'You must upload a .gif, .jpeg, or .png';
					} else if ($submitError == 'file-save') {
						$errorText = 'Error Saving File';
					} else if ($submitError == 'no-quality') {
						$errorText = 'You must choose a quality.';					
					} else {
						$errorText = 'Error submitting report';
					}
					?>
					<span class="submission-error"><?= $errorText ?></span>
				<? } ?>	
				
				<div class="field quality radio-menu first">
					<label for="quality">Session was:</label>
					<?
					foreach (ReportOptions::quality() as $key=>$value) {
						if ($this->report['quality'] == $key) {
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

				<div class="field text">
					<label for="text">Report:</label>
					<input type="text" name="text" id="text" value="<?=$this->report['text']?>" />
				</div>	

				<?
				if (isset($this->report['imagepath'])) {
					$image = getImageInfo($this->report['imagepath'], 200, 200);
					if (!empty($image)) {
						?>
						<div class="field image-container">
							<a href="<?=$image['src']?>" target="_blank"><image src="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/>
							</a>
							<label><input type="checkbox" name="delete-image" id="" value="true" /> Delete Image</label>	
						</div>
						<? 						
					}				
				}
				?>				
				
				<div class="field image last">
					<label for="upload">Upload <?=isset($this->report['imagepath']) ? 'new' : '';?> image:</label> 
					<input type="file" name="upload" id="upload" capture="camera">
					<span id="mobile-image-name" class="mobile-note">
						<?
						if($isMobile) {
							?>
							You will need <a href="itms-apps://itunes.com/apps/picup" target="_blank">Picup</a> to upload photos from your phone.
							<?
						}
						?>
					</span>
				</div>

				<input type="hidden" name="remoteImageURL" id="remoteImageURL" value="" />
				<input type="hidden" name="submit" value="update-report" />				
				<input type="submit" name="update_report" value="Update Report" />							
					
				
			</form>	
		<form action="" method="post" class="delete-form" id="delete-report-form">
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
				});

				$('#delete-btn-overlay #cancel-deletion').click(function(){
					$('#delete-btn-overlay').hide();
				});	
			})(jQuery);		
		</script>					
		</div>
		

		<?
	}	


}

?>