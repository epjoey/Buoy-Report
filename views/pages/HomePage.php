<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class HomePage extends Page {

	public function loadData(){
		parent::loadData();
		
		$this->pageTitle = "Home";

		
		/* load Report Filters */
		$this->reportFilters = array();
		$this->reportFilters['quality'] 	  = $_REQUEST['quality'];
		$this->reportFilters['image']   	  = $_REQUEST['image'];
		$this->reportFilters['text']    	  = $_REQUEST['text'];
		$this->reportFilters['obsdate']    	  = $_REQUEST['obsdate'];
		$this->reportFilters['locationIds']	  = Utils::pluck($this->user->locations, 'id');

		/* load Reports */
		$this->numReportsPerPage = 6;
		$this->reports = ReportService::getReportsForUserWithFilters($this->user, $this->reportFilters, array(
			'start' => 0,
			'limit' => $this->numReportsPerPage
		));

	}

	public function getBodyClassName() {
		return 'user-home-page';
	}	

	public function renderLeft() {
		FilterForm::renderFilterModule();
	}

	public function renderMain() {
		?>
		<div class="reports-container">
			<h2>Recent Reports</h2>		
			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<?
				FilterNote::renderFilterNote(array_merge($this->reportFilters, array(
					'location' => 'my locations'
				)));
				ReportFeed::renderFeed($this->reports, array(
					'limit' => $this->numReportsPerPage,
					'feedLocation' => 'homepage',
				));
				?>
			</div>
		</div>
		<?		
	}

	public function renderRight() {
		?>
		<div class="location-list">
			<h3>My Locations</h3>
			<?
			$list = new LocationList(array(
				'locations' => $this->user->locations,
				'showAddLocation' => TRUE,
				'showSeeAll' => TRUE
			));
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