<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
//include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/CrewList.php';


class AddCrewPage extends GeneralPage {

	private $addCrewError = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Submit Crew';
		
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Please enter a crew name';; break;
				case 2: $e = "Crew specified already exists"; break;
			}
			$this->addCrewError = $e;
		}		
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript" src="<?=Path::toJs()?>timezone.js"></script>	
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#locationname').focus();
				$("#add-loc-form").validate();
			});
		</script>
		<?
	}	

	public function getBodyClassName() {
		return 'add-location-page';
	}	


	public function afterSubmit() {
		
		//var_dump(Persistence::getCrewByName($_POST['name'])); exit;
		$crew = Persistence::getCrewByName($_POST['name']);
		if (!empty($crew)) { //TODO::test this. not sure what mysql_fetch_array returns when row not found.
			$error = 2;
			header('Location:'.Path::toSubmitCrew($error));
			exit();		
		}
		$crew['name'] = $_POST['name'] ? $_POST['name'] : null;
		$crew['description'] = $_POST['description'] ? $_POST['description'] : null;
		$crew['creator'] = $this->user->id;
		
		$newCrewId = Persistence::insertCrew($crew);
		Persistence::insertUserIntoCrew($this->user->id, $newCrewId);

		header('Location:'.Path::toCrew($newCrewId));
		exit();
	}

	public function renderBodyContent() {
		?>
			<h1 class="form-head">Submit New Crew</h1>
			<div class="form-container">
				<form action="" method="post" id="add-crew-form" class="" >
					<? if (isset($this->addCrewError)) { ?>
						<span class="submission-error"><?= $this->addCrewError ?></span>
					<? } ?>			
					<div class="field">
						<label for="name">Name</label>
						<input id="name" class="text-input required" name="name" type="text" placeholder="Enter Crew Name" />
					</div>
					<div class="field">
						<label for="description">Description</label>
						<input id="description" class="text-input required" name="description" type="text" placeholder="Enter Crew Description" />
					</div>					
					<div class="field">
						<input name="submit-crew" type="submit" value="Submit Crew" />
					</div>
				</form>
			</div>
		<?
	}

}
?>