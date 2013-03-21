<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/view/AddBuoyForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';



class EditBuoyPage extends Page {

	private $editBuoyError = NULL;
	private $buoyInfo = array();

	public function loadData($buoyId) {
		parent::loadData();
		$this->pageTitle = 'Edit Buoy ' . $buoyId;
		$this->buoyId = $buoyId;
		
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Enter a buoy number'; break;
				case 2: $e = 'No edits were specified'; break;
				case 3: $e = 'Buoy could not be reached'; break;
			}
			$this->editBuoyError = $e;
		}		

		$this->buoyInfo = BuoyService::getBuoy($this->buoyId);
		
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

		if (isset($_POST['delete-buoy'])) {
			Persistence::deleteBuoy($this->buoyId);
			header('Location:'.Path::toBuoys());
			exit();			
		}

		if (isset($_POST['enter-buoy'])) {
				
			if (empty($_POST['buoy-id'])) {
				$error = 1;
				header('Location:'.Path::toEditBuoy($this->buoyId, $error));
				exit();
			}

			if ($_POST['buoy-id'] == $this->buoyId && $_POST['buoy-name'] == $this->buoyInfo['name']) {
				$error = 2;
				header('Location:'.Path::toEditBuoy($this->buoyId, $error));
				exit();			
			}

			if ($_POST['buoy-id'] != $this->buoyId) {
				$buoyData = new Buoy($_POST['buoy-id']);
				if (!$buoyData->isValid()) {
					$error = 3;
					header('Location:'.Path::toEditBuoy($this->buoyId, $error));
					exit();
				}
			}			

			Persistence::updateBuoy($this->buoyId, $_POST['buoy-id'], $_POST['buoy-name']);
			header('Location:'.Path::toBuoys());
			exit();
		}
	}

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Edit Buoy</h1>
		<?
		AddBuoyForm::render(array(
			'status' => $this->editBuoyError, 
			'defaultBuoy' => $this->buoyInfo
		));
		?>
		<form action="" method="post" class="delete-form" id="delete-buoy-form">
			<input type="hidden" name="submit" value="delete-buoy" />
			<input type="button" id="delete-buoy-btn" class="delete-btn" value="Delete Buoy" />
			<div class="overlay" id="delete-btn-overlay" style="display:none;">
				<p>Are you sure you want to delete this Buoy?</p>
				<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
				<input class="confirm" type="submit" name="delete-buoy" id="confirm-deletion" value="Confirm"/>
			</div>
		</form>

		<script>
			$('#delete-buoy-btn').click(function(){
				$('#delete-btn-overlay').show();
				window.scrollTo(0,0);
			});

			$('#delete-btn-overlay #cancel-deletion').click(function(){
				$('#delete-btn-overlay').hide();
			});				
		</script>		
		<?		
	}

}
?>