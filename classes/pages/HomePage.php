<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/report/feed/FilterNote.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/report/feed/FilterNote.php';



class HomePage extends GeneralPage {

	public $newPost = null;
	public $newReport = null;


	public function loadData(){
		parent::loadData();
		$this->pageTitle = $this->siteTitle . ' Home';

		/* load Report Filters from URL */
		$this->reportFilters = FilterService::getReportFilterRequests(); 

		/* load Reports from DB */
		$this->reports = array();
		if ($this->user->locationIds) {
			$this->reportFilters['locations'] = $this->user->locationIds; //disregard filter request and use user's locations
			$this->reports = Persistence::getReports($this->reportFilters);
		}
	}

	public function getBodyClassName() {
		return 'user-home-page';
	}	

	public function renderLeft() {
		$filterOptions = array();
		$autoFilters = array(
			'locationIds' => $this->user->locationIds
		);		
		FilterForm::renderFilterModule($filterOptions, $autoFilters);
	}

	public function renderMain() {
		?>
		<div class="reports-container">
			<h2>Recent Reports</h2>		
			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<?
				$filterResults = array_merge(
					$this->reportFilters, array(
						'location' => 'my locations'
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
			<h3>My Locations</h3>
			<?
			if ($this->user->hasLocations) {
				$options['locations'] = $this->user->locations;
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