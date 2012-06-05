<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/report/feed/FilterNote.php';



class ProfilePage extends GeneralPage {

	protected $pageOwnerHasLocations = FALSE;

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
		if (!empty($this->pageOwnerLocations)) {
			$this->pageOwnerHasLocations = TRUE;		
		}	
		/* load Report Filters */
		$this->reportFilters = FilterService::getReportFilterRequests();
		$this->reportFilters['reporterId'] = $this->pageOwnerId; //disregard filter request and use user's locations

		/* load Reports */
		$this->reports = Persistence::getReports($this->reportFilters);		
	}

	public function getBodyClassName() {
		return 'profile-page';
	}		

	public function renderLeft() {
		$filterOptions = array(
			'locationObjects' => $this->pageOwnerLocations
		);
		$autoFilters = array(
			'reporterId' => $this->pageOwnerId
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
				?>
				<div id="report-feed">
					<?
					ReportFeed::renderFeed($this->reports); 
					?>
				</div>
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
		
		if ($this->pageOwnerHasLocations) {
			$options['locations'] = $this->pageOwnerLocations;
		}
		$options['showAddLocation'] = FALSE;
		$options['showSeeAll'] = TRUE;
		$list = new LocationList($options);
		$list->renderLocations();		
		
	}
}


?>