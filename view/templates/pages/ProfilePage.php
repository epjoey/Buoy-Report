<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class ProfilePage extends Page {

	public function getBodyClassName() {
		return 'profile-page';
	}		

	public function renderLeft() {
		$filterOptions = array(
			'locationObjects' => $this->reporter->locations
		);
		$autoFilters = array(
			'reporter' => $this->reporter->id
		);		
		FilterForm::renderFilterModule($filterOptions, $autoFilters);	
	}
	
	public function renderMain() {	
		?>
		<div class="reporter-info">
			<h1 class="name-title"><?=$this->reporter->name;?></h1>
			<p class="member-info">
				<?
				if (isset($this->reporter->joindate)) {
					print 'reporting since ' . date("F Y", strtotime($this->reporter->joindate));	
				}
				?>
			</p>	
		</div>	
		<div class="reports-container">
			<h2><span class="name"><?=$this->reporter->name;?></span>&apos;s Reports</h2>

			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<?
				$filterResults = array_merge(
					$this->reportFilters, array(
						'location' => $this->reporter->name . "'s locations"
					)
				);
				FilterNote::renderFilterNote($filterResults);
				ReportFeed::renderFeed($this->reports, array(
					'limit' => $this->numReportsPerPage,
					'reportFilters' => $this->reportFilters
				));
				?>
			</div>						
		</div>	
		<?
	}			
	public function renderRight() {
		?>
		<div class="location-list">
			<h3><?=$this->reporter->name;?>&apos;s Locations</h3>
			<? $this->renderLocations() ?>	
		</div>		
		<?
	}

	public function renderLocations() {
		$list = new LocationList(array(
			'locations' => $this->reporter->locations,
			'showAddLocation' => FALSE,
			'showSeeAll' => TRUE
		));
		$list->renderLocations();
	
	}
}


?>