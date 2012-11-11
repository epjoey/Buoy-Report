<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/modules/SearchModule.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ItemList.php';



class ReporterPage extends Page {

	public $reporters = NULL;
	public $isLocationReporters = FALSE;
	public $locationId = NULL;
	public $locInfo = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Reporters';
		if (isset($_GET['location']) && $_GET['location']) {
			$this->locationId = $_GET['location'];	
			$this->locInfo = Persistence::getLocationInfoById($this->locationId);
			if (!isset($this->locInfo)) {
				header('Location:'.Path::to404());
				exit();
			} 
			$this->isLocationReporters = TRUE;		
			$reporterIds = Persistence::getUsersByLocation($this->locationId);	
			foreach($reporterIds as $id) {
				$this->users[] = Persistence::getUserInfoById($id['reporterid']);
			} 
			$this->pageTitle = $this->locInfo['locname'] . ' Reporters';			
		} else {
			$this->users = Persistence::getUsers();
		}
		$this->searchModule = new SearchModule;
	}

	public function getBodyClassName() {
		return 'reporter-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		$this->searchModule->renderFilterJs();
	}

	public function renderBodyContent() {
		?>
		<h1 class="list-title">
			<? 
			if($this->isLocationReporters) {
				?>
				<a href="<?=Path::toLocation($this->locInfo['id'])?>"><?=$this->locInfo['locname']?></a> 
				<?
			} else {
				print 'Buoy';
			} 
			?> 
			Reporters
		</h1>

		<div class="search-container">
			<? $this->searchModule->renderFilterInput(); ?>
		</div>

		<div class="rep-page-list">
			<?
			$options['items'] = $this->users;
			$count = count($options['items']);
			for($i = 0; $i < $count; $i++) {
				$options['items'][$i]['path'] = Path::toProfile($options['items'][$i]['id']);
			} 
			$options['itemLabel'] = 'reporter';
			$options['pathToAll'] = Path::toReporters();
			$options['isSearchable'] = TRUE;
			ItemList::renderList($options);
			?>
		</div>
		<?
	}

}