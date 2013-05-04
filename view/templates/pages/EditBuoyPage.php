<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class EditBuoyPage extends Page {


	public function getBodyClassName() {
		return 'add-buoy-page';
	}

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Edit Buoy <?= $this->buoyId ?></h1>
		<?
		EditBuoyForm::render(array(
			'status' => null, 
			'buoy' => $this->buoy
		));
		DeleteBuoyForm::render(array(
			'status' => null, 
			'buoy' => $this->buoy
		));
	}

}
?>