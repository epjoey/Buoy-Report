<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditLocationPage extends LocationDetailPage {


	public function getBodyClassName() {
		return 'edit-location-page';
	}	
	
	public function renderJs() {
		parent::renderJs();
	}		

	public function renderForbiddenMessage(){
		?>
		<p class="forbidden-message overlay">
			You must be the creator of the location to edit the location. However, you can still add forecast links, bouys, and tidestation (if the creator hasn't done so yet). <a href="<?=Path::toLocation($this->locationId);?>">Return to Location</a>
		</p>
		<?
	}

	public function renderBodyContent() {
		if ($this->location->creator != $this->user->id) {
			$this->renderForbiddenMessage();
		}
		else {
			$this->renderForm();		
		}		
		
	}

	public function renderForm() {
		$device = new Mobile_Detect();

		?>
		<h2>
			<a href="<?=Path::toLocation($this->locationId)?>"><?= html($this->location->locname)?></a> > Edit
		</h2>
		<div class="form-container">
			<?
			if (isset($this->editLocationError)) {
				?>
				<span class="submission-error"><?= html($this->editLocationError) ?></span>
				<?
			}
			?>						
			<form method="post" action="<?=Path::toEditLocationPost()?>" id="edit-location-name-form" enctype="multipart/form-data">
				<div class="field">
					<input type="text" name="locname" class="text-input required" value="<?=html($this->location->locname)?>" />
				</div>

				<? $zones = timezone_identifiers_list(); ?>
				<div class="field">
					<select name="timezone">
					<?
						if (isset($this->location->timezone)) {
							?>
							<option value="<?= $this->location->timezone ?>" selected="selected"><?= $this->location->timezone ?> (<?= getOffset($this->location->timezone)/3600; ?>)</option>
							<?
						}	
						foreach ($zones as $tz) {
							$offset = getOffset($tz)/3600;
							?>
							<option value="<?= $tz ?>"><?= $tz ?> (<?= $offset ?>)</option>			
							<?
						}
					?>
					</select>
					
				</div>
				<div class="field">
					<? /*
					// Add Sublocations
					foreach($this->locations as $location) {
						if($location->id != $this->locationId){
							?>
							<div class="input-field">
								<a href="<?=Path::toLocation($location->id)?>"><?= $location->locname ?></a>
								<input type="checkbox" name="sublocationid" value="<?= $location->id ?>"/>
							</div>
							<?
						}
					} */
					?>
				</div>				
				<div class="field image last">
					<? FormFields::renderImageInput($this->location->coverImagePath) ?>
				</div>
				<div class="field submit">
					<input type="hidden" name="locationId" value="<?= $this->location->id ?>" />
					<input type="submit" name="select-timezone" value="Update Location" />
				</div>
			</form>
			<form action="<?=Path::toDeleteLocation()?>" method="post" class="delete-form" id="delete-location-form">
				<input type="hidden" name="locationId" value="<?= $this->location->id ?>" />
				<input type="button" id="delete-location-btn" class="delete-btn" value="Delete Location" />
				<div class="overlay" id="delete-btn-overlay" style="display:none;">
					<p>Are you sure you want to delete this location? <strong>All reports will be deleted forever!</strong></p>
					<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
					<input class="confirm" type="submit" name="delete-location" id="confirm-deletion" value="Confirm"/>
				</div>
			</form>

			<script>
				$('#delete-location-btn').click(function(){
					$('#delete-btn-overlay').show();
					window.scrollTo(0,0);
				});

				$('#delete-btn-overlay #cancel-deletion').click(function(){
					$('#delete-btn-overlay').hide();
				});				
			</script>

		</div>
		<?
	}

	


}