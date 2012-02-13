<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';



class HomePage extends GeneralPage {

	public $newPost = null;
	public $newReport = null;


	public function loadData(){
		parent::loadData();
		$this->pageTitle = $this->siteTitle . ' Home';
	}

	public function getBodyClassName() {
		return 'user-home-page';
	}	

	public function renderLeft() {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				$filterform = new FilterForm;
				$options['showlocations'] = FALSE;
				$filterform->renderFilterForm($options);
				?>
			</div>
		</div>
		<?
	}

	public function renderMain() {
		?>
		<div class="reports-container">
			<h2>Recent Reports</h2>		
			<?
			$options['locations'] = $this->userLocations;
			$options['on-page'] = 'homepage';			
			$reports = new ReportFeed;
			$reports->loadData($options);
			$reports->renderFilterIcon();
			?>
			<div id="report-feed-container" onPage="homepage">
				<?
				$reports->renderReportFeed();
				?>
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
			<h3>My Locations</h3>
			<?
			if ($this->userHasLocations) {
				$options['locations'] = $this->userLocations;
			}
			$options['showAddLocation'] = TRUE;
			$options['showSeeAll'] = TRUE;
			$list = new LocationList($options);
			$list->renderLocations();
			?>
		</div>
		<?
	}

	private function renderHowTo(){
		?>
		<h3>Hey Reporter!</h3>
		<p>If your local spot is not set up already, simply add the location along with any nearby buoys/tide stations. Next time you go, don't forget to snap a photo. You can upload the photo along with a rating of the conditions and an optional description of your session from your smart phone or computer at home. You'll have an insightful record of that session online!<p>
		<?
	}

}
?>