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
		?>	
		<div class="form-container report-form-container">
			<form id="report-form" action="<?=Path::toHandleReportSubmission()?>" enctype="multipart/form-data" method="post" >	
				<span class="submission-error"><?= $statusMsg ?></span>
				<?
		
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