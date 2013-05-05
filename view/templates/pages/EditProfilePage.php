<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditProfilePage extends Page {

	public function getBodyClassName() {
		return 'edit-profile-page';
	}	


	public function renderLeft() {
		$filterOptions = array(
			'locationObjects' => $this->user->locations
		);
		$autoFilters = array(
			'reporter' => $this->user->id
		);		
		FilterForm::renderFilterModule($filterOptions, $autoFilters);	
	}
	
	public function renderMain() {
		?>
		<h1>My account</h1>
		<?
		$this->renderEditInfo();
		$this->renderMyReports();
		
	}

	public function renderMyReports() {
		?>
		<div class="reports-container">
			<h3>My Reports</h3>
			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<?
				$filterResults = array_merge(
					$this->reportFilters, array(
						'location' => 'my locations'
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

	public function renderEditInfo() {
		?>
		<div class="account-details">
			<h3>Account Settings</h3>
			<? EditAccountForm::renderForm($this->user, $this->editAccountStatus); ?>
		</div>
		<?
	}	

	public function renderRight() {	
		?>
		<div class="location-list">
			<h3>My Locations</h3>
			<? $this->renderMyLocations() ?>	
		</div>		
		<?		
	}
	
	public function renderMyLocations() {
		$options['locations'] = $this->user->locations;
		$options['showAddLocation'] = TRUE;
		$options['showSeeAll'] = TRUE;
		$list = new LocationList($options);
		$list->renderLocations();			
	}	
}
?>