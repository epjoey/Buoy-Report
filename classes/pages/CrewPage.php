<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';


class CrewPage extends GeneralPage {


	public function loadData() {
		parent::loadData();
		$this->crewId = $_GET['id'];
		$this->crewInfo = Persistence::getCrewById($this->crewId);
		if (!isset($this->crewInfo)) {
			header('Location:'.Path::to404());
			exit();
		}			
		
		$this->creator = Persistence::getUserInfoById($this->crewInfo['creator']);
		$this->pageTitle = $this->crewInfo['name'];		
		$this->reporters = Persistence::getUsersByCrew($this->crewId);
							
	}

	public function getBodyClassName() {
		return 'crew-detail-page';
	}		

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">					
		</script>
		<?
	}	

	public function afterSubmit() {

		if ($_REQUEST['submit'] == 'bookmark') {

		}

		if ($_REQUEST['submit'] == 'un-bookmark') {

		}		
	}

	public function renderLeft() {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				$filterform = new FilterForm;
				$filterform->renderFilterForm();
				?>
			</div>
		</div>
		<?
	}
	
	public function renderMain() {
		$this->renderCrewDetails();
	}	

	public function renderCrewDetails() {
		?>
		<div class="loc-details">
			<h1><?= html($this->crewInfo['name'])?></h1>
			<h2><?= html($this->crewInfo['description'])?></h2>
			<h3><?= html($this->creator['name'])?></h3>
			<? var_dump($this->crewInfo) ?>
		</div>
		<?
	}


	public function renderRight() {

	}

	private function renderCrewInfo() {
		?>			
		<div class="loc-meta">
			<h3>Crew Info</h3>
			<div class="reporters">

			</div>
		</div>	
		<?
	}
	


}