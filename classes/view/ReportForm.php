<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';



class ReportForm {

	public function renderReportForm($locInfo, $reporterInfo, $submitError = NULL, $needPicup) {
		if (!in_array($locInfo, $reporterInfo['locations'])) {
			$reporterHasLocation = 0;
		} else {
			$reporterHasLocation = 1;
		}
		?>
		<h1 class="form-head">Post Report For <a href="<?=Paths::toLocation($locInfo['id']);?>" id="location"><?= $locInfo['locname'] ?></a>
		</h1>		
		<div class="form-container">
			<form id="report-form" action="<?=Paths::toHandleReportSubmission()?>" enctype="multipart/form-data" method="post" >	
			
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
			
			
				<div class="field time first">
					<label for="time_offset">Time:</label>
					<select name="time_offset" id="time-offset">
						<option value="0">Now</option>
						<?
						for ($i=1; $i <= 48; $i++) { 
							?>
							<option value="-<?=$i?>"><?= $i . " hours ago" ?></option>
							<?
						}
						?>
					</select>
				</div>
				<div class="field quality radio-menu">
					<label for="quality">Session was*:</label>
					<?
					foreach (ReportOptions::quality() as $key=>$value) {
						?>
						<span class="radio-field">
							<input type="radio" class="required" name="quality" id="quality-<?=$key?>" value="<?=$key?>" /><label for="quality-<?=$key?>"> <?=$value?></label>
						</span>
						<?
					}
					?>
				</div>
				<div class="field text">
					<label for="text">Report:</label>
					<input type="text" name="text" id="text" placeholder="how was it?" />
				</div>
				<div class="field radio-menu include">	
					<label>Include:</label>
					<?
					/* use js to get values of attributes on location selector */
					if (isset($locInfo['buoy1'])) {
						?>
						<span class="radio-field">
							<input type="checkbox" name="buoy1" id="buoy1" value="<?=$locInfo['buoy1']?>" checked='checked' /><label for="buoy1"> Buoy <?=$locInfo['buoy1']?></label>
						</span>	
						<?
					}
					if (isset($locInfo['buoy2'])) {
						?>
						<span class="radio-field">
							<input type="checkbox" name="buoy2" id="buoy2" value="<?=$locInfo['buoy2']?>" checked='checked' /><label for="buoy2"> Buoy <?=$locInfo['buoy2']?></label>
						</span>	
						<?						
					}
					if (isset($locInfo['buoy3'])) {
						?>
						<span class="radio-field">
							<input type="checkbox" name="buoy3" id="buoy3" value="<?=$locInfo['buoy3']?>" checked='checked' /><label for="buoy3"> Buoy <?=$locInfo['buoy3']?></label>
						</span>	
						<?						
					}					
					if (isset($locInfo['tidestation'])) {
						?>
						<span class="radio-field">
							<input type="checkbox" name="tidestation" id="tidestation" value="<?=$locInfo['tidestation']?>" checked='checked' /><label for="tidestation"> Tidestation <?=$locInfo['tidestation']?></label>
						</span>	
						<?						
					}
					if (!isset($locInfo['tidestation']) && !isset($locInfo['buoy1']) && !isset($locInfo['buoy2']) && !isset($locInfo['buoy3'])) {
						?>
						<span>No buoys or tidestations to include. <a href="<?=Paths::toLocation($locInfo['id']);?>">Add one</a></span> 
						<?
					}
					?>				
				</div>		
				<div class="field image last">
					<label for="upload">Upload an image:</label> 
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
				<input type="hidden" name="remoteImageURL" id="remoteImageURL" value="" />
				<input type="hidden" name="reporterid" value="<?=$reporterInfo['id']?>" />
				<input type="hidden" name="reportStatus" value="<?=$reporterInfo['reportStatus']?>" />
				<input type="hidden" name="locationid" value="<?=$locInfo['id']?>" />
				<input type="hidden" name="locationname" value="<?=$locInfo['locname']?>" /> 
				<input type="hidden" name="reporterhaslocation" value="<?=$reporterHasLocation?>" />
				<input type="hidden" name="submit" value="submit-report" />				
				<input type="submit" name="submit_report" value="Submit Report" />
			</form>
		</div>
	<?	
	}
}
?>