<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationPage extends Page {

	//whether the locations link straight to the report form
	private $isToPost = FALSE;

	//whether the locations are for a specific reporter
	private $isReporterLocations = FALSE;

	//whether the locations are for the current user
	private $isCurrentUserLocations = FALSE;

	//the reporter whose locations are listed
	private $reporterId = NULL;
	private $reporterInfo = NULL;


	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Locations';
		
		if (isset($_GET['reporter']) && $_GET['reporter']) {
			$this->reporterId = $_GET['reporter'];			
		}

		if (isset($this->reporterId)) {
			$this->isReporterLocations = TRUE;
		}	

		if ($this->user->isLoggedIn && $this->reporterId == $this->user->id) {
			$this->isCurrentUserLocations = TRUE;
		}

		if ($this->isCurrentUserLocations) {
			$this->locations = $this->user->locations;

		} elseif ($this->isReporterLocations) {

			$this->reporterInfo = Persistence::getUserInfoById($this->reporterId);
			if (!isset($this->reporterInfo)) {
				header('Location:'.Path::to404());
				exit();
			}			
			$this->locations = LocationService::getReporterLocations($this->reporterId);

		} else {
			$this->locations = Persistence::getLocations();

		}

		if (isset($_GET['post']) && $_GET['post'] == 'true') {
			$this->isToPost = TRUE;
		}
	}

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
				$name = $this->reporterInfo['name'] . "'s";
			}
			?>
			<h1 class="list-title"><?= html($name) ?> Locations</h1>
			<? 
		}
		if (!$this->isReporterLocations) {
			?>
			<div class="search-container">
				<? SearchModule::renderFilterInput('Locations'); ?>
			</div>
			<?			
		}
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