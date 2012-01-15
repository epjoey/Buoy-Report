<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/modules/SearchModule.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';



class LocationPage extends GeneralPage {

	public $isToPost = FALSE;
	public $isUserLocations = FALSE;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Locations';
		if (isset($_GET['reporter']) && $_GET['reporter']) {
			$this->reporterId = $_GET['reporter'];
			if ($this->reporterId != $this->userId) {
				$this->locations = Persistence::getUserLocations($this->reporterId);	
				if (!isset($this->locations)) {
					header('Location:'.Paths::to404());
					exit();
				}
			} else {
				$this->locations = $this->userLocations;
			}
			$this->isUserLocations = TRUE;
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
		?>
		<h1 class="list-title"><?= $this->isUserLocations ? 'My ' : '';?> Locations</h1>
		<? 
		if (!$this->isUserLocations) {
			?>
			<div class="search-container">
				<? $this->searchModule->renderFilterInput(); ?>
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