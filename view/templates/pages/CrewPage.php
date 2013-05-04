<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class CrewPage extends Page {


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
		FilterForm::renderFilterModule();
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