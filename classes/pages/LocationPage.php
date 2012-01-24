<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/modules/SearchModule.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';



class LocationPage extends GeneralPage {

	private $isToPost = FALSE;
	private $isUserLocations = FALSE;
	private $isCurrentUserLocations = FALSE;
	private $reporterId = NULL;
	private $reporterInfo = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Locations';
		
		if (isset($_GET['reporter']) && $_GET['reporter']) {
			$this->reporterId = $_GET['reporter'];			
		}

		if (isset($this->reporterId)) {
			$this->isUserLocations = TRUE;
		}	

		if ($this->reporterId == $this->userId) {
			$this->isCurrentUserLocations = TRUE;
		}

		if ($this->isCurrentUserLocations) {
			$this->locations = $this->userLocations;
			$this->reporterInfo = $this->userInfo;

		} elseif ($this->isUserLocations) {
			$this->reporterInfo = Persistence::getReporterInfoById($this->reporterId);
			if (!isset($this->reporterInfo)) {
				header('Location:'.Paths::to404());
				exit();
			}			
			$this->locations = Persistence::getUserLocations($this->reporterId);	

		} else {
			$this->locations = Persistence::getLocations();

		}

		$this->searchModule = new SearchModule;
		if (isset($_GET['post']) && $_GET['post'] == 'true') {
			$this->isToPost = TRUE;
		}
	}

	public function getBodyClassName() {
		return 'location-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		$this->searchModule->renderFilterJs();
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
			} elseif ($this->isUserLocations) {
				$name = $this->reporterInfo['name'] . "'s";
			}
			?>
			<h1 class="list-title"><?= $name ?> Locations</h1>
			<? 
		}
		if (!$this->isUserLocations) {
			?>
			<div class="search-container">
				<? $this->searchModule->renderFilterInput('Locations'); ?>
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