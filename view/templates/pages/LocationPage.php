<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationPage extends Page {


	public function getBodyClassName() {
		return 'location-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		SearchModule::renderFilterJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#query').focus();
			});
		</script>
		<?		
	}

	public function renderBodyContent() {
		if ($this->isToPost) {
			?>
			<h1 class="list-title">Choose a location:</h1>
			<?
		} else {
			$name = '';
			if ($this->isCurrentUserLocations) {
				$name = 'My';
			} elseif ($this->isReporterLocations) {
				$name = $this->reporter->name . "'s";
			}
			?>
			<h1 class="list-title"><?= html($name) ?> Locations</h1>
			<? 
		}
		?>
		<div class="search-container">
			<? SearchModule::renderFilterInput('Locations'); ?>
		</div>
		<?			
		?>

		<div class="loc-page-list">
			<div class="grid-list-container" id="grid-list-container">
				<?
				$options['locations'] = $this->locations;
				$options['toPost'] = $this->isToPost;
				$options['showAddLocation'] = TRUE;
				$options['showSeeAll'] = TRUE;
				$options['isSearchable'] = TRUE;
				$list = new LocationList($options);
				$list->renderLocations();
				?>
			</div>
		</div>
		<?
	}

}