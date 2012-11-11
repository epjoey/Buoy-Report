<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/AddBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';



class AddBuoyPage extends Page {

	private $addBuoyError = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Submit Buoy';
		
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Enter a buoy number'; break;
				case 2: $e = 'Buoy already exists in database'; break;
				case 3: $e = 'Buoy could not be reached'; break;
			}
			$this->addBuoyError = $e;
		}		
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript" src="<?=Path::toJs()?>timezone.js"></script>	
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#buoy-id').focus();
				$("#add-buoy-form").validate();
			});
		</script>
		<?
	}	

	public function getBodyClassName() {
		return 'add-buoy-page';
	}	


	public function afterSubmit() {

		if (empty($_POST['buoy-id'])) {
			$error = 1;
			header('Location:'.Path::toAddBuoy($error));
			exit();
		}
		

		if (Persistence::dbContainsBuoy($_POST['buoy-id'])) {
			$error = 2;
			header('Location:'.Path::toAddBuoy($error));
			exit();			
		}

		$buoyData = new Buoy($_POST['buoy-id']);
		if (!$buoyData->isValid()) {
			$error = 3;
			header('Location:'.Path::toAddBuoy($error));
			exit();
		}			

		$newBuoyId = Persistence::insertBuoy($_POST['buoy-id'], $_POST['buoy-name']);
		header('Location:'.Path::toBuoys());
		exit();
	}

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Submit Buoy</h1>
		<?
		$showByDefult = true;
		$form = new AddBuoyForm;
		$form->renderAddBuoyForm($this->addBuoyError, $showByDefult);
	}

}
?>