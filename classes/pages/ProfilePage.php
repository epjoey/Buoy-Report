<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/FilterNote.php';



class ProfilePage extends Page {


	public function loadData(){
		parent::loadData();
		$this->pageOwnerId = intval($_GET['reporter']);
		$this->pageOwnerInfo = Persistence::getUserInfoById($this->pageOwnerId);
		if (!$this->pageOwnerInfo) {
			header('Location:'.Path::to404());
			exit();
		}
		$this->pageOwnerName = $this->pageOwnerInfo['name'];
		$this->pageOwnerEmail = $this->pageOwnerInfo['email'];
		$this->pageTitle = $this->pageOwnerName . '\'s Reporter Profile';
		$this->pageOwnerLocations = Persistence::getUserLocations($this->pageOwnerId);
		//$this->pageOwnerLocations = LocationService::getReporterLocations($this->pageOwnerId);
		

		/* load Report Filters */
		$this->reportFilters = array();
		$this->reportFilters['quality'] 	  = $_REQUEST['quality'];
		$this->reportFilters['image']   	  = $_REQUEST['image'];
		$this->reportFilters['text']    	  = $_REQUEST['text'];
		$this->reportFilters['obsdate']    	  = $_REQUEST['obsdate'];
		$this->reportFilters['locationIds']   = $_REQUEST['location'] ? array($_REQUEST['location']) : array();
		$this->reportFilters['reporterId']	  = $this->pageOwnerId;

		/* load Reports */
		$this->reports = ReportService::getReportsForUserWithFilters($this->user, $this->reportFilters);		
	}

	public function getBodyClassName() {
		return 'profile-page';
	}		

	public function renderLeft() {
		$filterOptions = array(
			'locationObjects' => $this->pageOwnerLocations
		);
		$autoFilters = array(
			'reporter' => $this->pageOwnerId
		);		
		FilterForm::renderFilterModule($filterOptions, $autoFilters);	
	}
	
	public function renderMain() {	
		?>
		<div class="reporter-info">
			<h1 class="name-title"><?=$this->pageOwnerName;?></h1>
			<p class="member-info">
				<?
				if (isset($this->pageOwnerInfo['joindate'])) {
					print 'reporting since ' . date("F Y", strtotime($this->pageOwnerInfo['joindate']));	
				}
				?>
			</p>	
		</div>	
		<div class="reports-container">
			<h2><span class="name"><?=$this->pageOwnerName;?></span>&apos;s Reports</h2>

			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<?
				$filterResults = array_merge(
					$this->reportFilters, array(
						'location' => $this->pageOwnerName . "'s locations"
					)
				);
				FilterNote::renderFilterNote($filterResults);
				ReportFeed::renderFeed($this->reports);
				?>
			</div>						
		</div>	
		<?
	}			
	public function renderRight() {
		?>
		<div class="location-list">
			<h3><?=$this->pageOwnerName;?>&apos;s Locations</h3>
			<? $this->renderLocations() ?>	
		</div>		
		<?
	}

	public function renderLocations() {
		$list = new LocationList(array(
			'locations' => $this->pageOwnerLocations,
			'showAddLocation' => FALSE,
			'showSeeAll' => TRUE
		));
		$list->renderLocations();
	
	}
}


?>