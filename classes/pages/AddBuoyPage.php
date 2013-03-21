<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/view/AddBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';



class AddBuoyPage extends Page {

	private $addBuoyError = NULL;

	public function loadData() {
		$this->pageTitle = 'Submit Buoy';	
		parent::loadData();
	}	

	public function getBodyClassName() {
		return 'add-buoy-page';
	}

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Submit Buoy</h1>
		<?
		AddBuoyForm::render();
	}

}
?>