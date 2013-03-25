<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class AddBuoyPage extends Page {

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