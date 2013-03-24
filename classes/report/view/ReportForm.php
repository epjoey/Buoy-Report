<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/SingleReport.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportFormFields.php';



class ReportForm {

	static function renderReportForm($location, $user, $submitError = NULL, $needPicup) {
		?>
		<h1 class="form-head">Post Report For <a href="<?=Path::toLocation($location->id);?>" id="location"><?= $location->locname ?></a>
		</h1>		
		<div class="form-container">
			<form id="report-form" action="<?=Path::toHandleReportSubmission()?>" enctype="multipart/form-data" method="post" >	
				
				<? 
				if (isset($submitError)) {
					if ($submitError == 'upload-file') {
						$submitError = 'Error uploading file';
					} else if ($submitError == 'file-type') {
						$submitError = 'You must upload a .gif, .jpeg, or .png';
					} else if ($submitError == 'file-save') {
						$submitError = 'Error Saving File';
					} else if ($submitError == 'no-quality') {
						$submitError = 'You must choose a quality.';					
					}
					?>
					<span class="submission-error"><?= $submitError ?></span>
				<? 
				} 

				ReportFormFields::renderTimeSelect();
				?>
			
				<div class="field quality radio-menu required">
					<label for="quality">Quality of Rides:</label>
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
				<div class="optional-fields <?= $location->sublocations ? 'includes-sublocations' : ''?> ">
					<h5 class="form-heading">Optional Fields</h5>
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
						<div class="field radio-menu include">	
							<label>Include:</label>
							<div class="include-fields">
								<?
								/* use js to get values of attributes on location selector */
								foreach ($location->buoys as $buoy) {
									?>
									<span class="radio-field">
										<input type="checkbox" id="buoy-<?=$buoy->buoyid ?>" name="buoys[]" value="<?=$buoy->buoyid ?>" checked='checked' /><label for="buoy-<?=$buoy->buoyid ?>"> Buoy <?=$buoy->buoyid ?> (<?=$buoy->name ?>)</label>
									</span>	
									<?
								}
								foreach ($location->tideStations as $ts) {
									?>
									<span class="radio-field">
										<input type="checkbox" id="ts-<?=$ts->stationid ?>" name="tidestations[]" value="<?=$ts->stationid ?>" checked='checked' /><label for="ts-<?=$ts->stationid ?>"> Tide Station <?=$ts->stationid ?> (<?=$ts->stationname ?>)</label>
									</span>	
									<?
								}											
								if (!$location->tideStations && !$location->buoys) {
									?>
									<span>No buoys or tidestations to include. <a href="<?=Path::toLocation($location->id);?>">Add one</a></span> 
									<?
								}
								?>
							</div>		
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
					</div>
				</div><!--end optional fields-->
				<input type="hidden" name="remoteImageURL" id="remoteImageURL" value="" />
				<input type="hidden" name="reporterid" value="<?=$user->id?>" />
				<input type="hidden" name="public" value="<?=$user->privacySetting?>" />
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