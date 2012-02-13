<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';




class ProfilePage extends GeneralPage {

	protected $pageOwnerHasLocations = FALSE;

	public function loadData(){
		parent::loadData();
		$this->pageOwnerId = $_GET['reporter'];
		$this->pageOwnerInfo = Persistence::getReporterInfoById($this->pageOwnerId);
		if (!isset($this->pageOwnerInfo)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::to404());
			exit();
		}
		$this->pageOwnerName = $this->pageOwnerInfo['name'];
		$this->pageOwnerEmail = $this->pageOwnerInfo['email'];
		$this->pageTitle = $this->pageOwnerName . '\'s Reporter Profile';
		$this->pageOwnerLocations = Persistence::getUserLocations($this->pageOwnerId);
		if (!empty($this->pageOwnerLocations)) {
			$this->pageOwnerHasLocations = TRUE;		
		}	
	}

	public function getBodyClassName() {
		return 'profile-page';
	}		

	public function renderLeft() {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				$filterform = new FilterForm;
				$options['showlocations'] = TRUE;
				$options['locations'] = $this->pageOwnerLocations;
				$filterform->renderFilterForm($options);
				?>
			</div>
		</div>	
		<?
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
			<?			
			$options['locations'] = $this->pageOwnerLocations;
			$options['reporters'] = array($this->pageOwnerInfo);
			$options['on-page'] = 'profile-page';			
			$reports = new ReportFeed;
			$reports->loadData($options);
			$reports->renderFilterIcon();				
			?>
			<div id="report-feed-container" onPage="profile-page">		
				<? $reports->renderReportFeed(); ?>
			</div>
			<?
			$reports->renderReportFeedJS();
			?>							
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