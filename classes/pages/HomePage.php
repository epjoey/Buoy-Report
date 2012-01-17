<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
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

	public function afterSubmit() {
		if ($_POST['submit'] == "submit-report") {

			//validating and storing report in Session, then redirecting back to report form or same page
			$newReport = new Report;
			if(!$newReport->handleSubmission()) {
				header('Location:'.Paths::toPostReport($newReport->reportInfo['locId'], $newReport->submitError));
				exit();
			}
			//redirect back to same page, report feed will check session for new report and ajax post
			header('Location:'.Paths::toUserHome());
			exit();
		}
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
			<div id="report-feed-container">
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

}
?>