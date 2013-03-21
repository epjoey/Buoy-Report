<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/view/EditBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/view/DeleteBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';



class EditBuoyPage extends Page {

	private $editBuoyError = NULL;
	private $buoyInfo = array();

	public function loadData($buoyId) {
		parent::loadData();
		$this->buoyId = $buoyId;
		$this->buoy = BuoyService::getBuoy($this->buoyId);
		if (!$this->buoy) {
			header('Location:'.Path::to404());
			exit();			
		}
		$this->pageTitle = 'Edit Buoy ' . $buoyId;
	}


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
		?>
		
		<?		
	}

}
?>