<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/modules/SearchModule.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/GridList.php';



class ReporterPage extends GeneralPage {

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
				header('Location:'.Paths::to404());
				exit();
			} 
			$this->isLocationReporters = TRUE;		
			$reporterIds = Persistence::getReportersByLocation($this->locationId);	
			foreach($reporterIds as $id) {
				$this->reporters[] = Persistence::getReporterInfoById($id['reporterid']);
			} 
			$this->pageTitle = $this->locInfo['locname'] . ' Reporters';			
		} else {
			$this->reporters = Persistence::getReporters();
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
				<a href="<?=Paths::toLocation($this->locInfo['id'])?>"><?=$this->locInfo['locname']?></a> 
				<?
			} else {
				print 'Bouy';
			} 
			?> 
			Reporters
		</h1>

		<div class="search-container">
			<? $this->searchModule->renderFilterInput(); ?>
		</div>

		<div class="rep-page-list">
			<?
			$options['items'] = $this->reporters;
			$count = count($options['items']);
			for($i = 0; $i < $count; $i++) {
				$options['items'][$i]['path'] = Paths::toProfile($options['items'][$i]['id']);
			} 
			$options['itemLabel'] = 'reporter';
			$options['showSeeAllLink'] = TRUE;
			$options['pathToAll'] = Paths::toReporters();
			$options['isSearchable'] = TRUE;
			$list = new GridList($options);
			$list->renderGridList();
			?>
		</div>
		<?
	}

}