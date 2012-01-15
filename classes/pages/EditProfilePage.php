<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/magicquotes.php';


class EditProfilePage extends GeneralPage {

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'My Account';	
		$this->editAccountError = NULL;	
	}


	public function afterSubmit() {
		if ($_POST['submit'] == 'edit-account') {
			if (empty($_POST['current-password'])) {
				$this->editAccountError = 'You must enter your current password to edit account';
				$this->renderPage();
				exit();				
			}

			$reporterId = Persistence::returnReporterId($this->userEmail, md5($_POST['current-password'] . 'reportdb'));
			
			if (!isset($reporterId)) {
				$this->editAccountError = 'The password you entered is not correct';
				$this->renderPage();
				exit();
			}
			if (!empty($_POST['new-password']) && strlen($_POST['new-password']) < 5) {
				$this->editAccountError = 'Password must be more than 5 characters';
				$this->renderPage();
				exit();				
			}
			if (!empty($_POST['new-email']) && filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL) != TRUE ) {
				$this->editAccountError = 'You must enter a valid email address';
				$this->renderPage();
				exit();					
			}

			$options = array();
			if (!empty($_POST['new-email']) && $_POST['new-email'] != $this->userEmail) {
				$options['newEmail'] = $_POST['new-email'];
			}
			if (!empty($_POST['new-name']) && $_POST['new-name'] != $this->userName) {
				$options['newName'] = $_POST['new-name'];
			} 
			if (!empty($_POST['new-password'])) {
				$options['newPassword'] = md5($_POST['new-password'] . 'reportdb');
			} 	
			if (count($options) > 0) {
				Persistence::updateUserInfo($this->userId, $options);
				$this->user->updateUserSession($options);		
			} else {
				$this->editAccountError = 'No changes were specified';
				$this->renderPage();
				exit();					
			}
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.Paths::toProfile($this->userId));
			exit();		
		}
		
		if ($_POST['submit'] == 'delete-reporter') {
			Persistence::deleteReporter($this->userId);
			header('Location:'.Paths::toLogout());
			exit();	
		}
	}	

	public function renderLeft() {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				$filterform = new FilterForm;
				$options['showlocations'] = TRUE;
				$options['locations'] = $this->userLocations;					
				$filterform->renderFilterForm($options);
	
				?>
			</div>
		</div>			
		<?
	}
	public function renderMain() {
		?>
		<h1>My account</h1>
		<?
		$this->renderMyReports();
		$this->renderEditInfo();
	}

	public function renderMyReports() {
		?>
		<div class="reports-container">
			<h3>My Reports</h3>
			<?
			$options['locations'] = $this->userLocations;
			$options['on-page'] = 'edit-profile-page';	
			$reports = new ReportFeed;
			$reports->loadData($options);
			$reports->renderFilterIcon();							
			?>
			<div id="report-feed-container">		
				<? $reports->renderReportFeed(); ?>
			</div>	
			<?
			$reports->renderReportFeedJS();
			?>							
		</div>
		<?
	}	

	public function renderEditInfo() {
		?>
		<div class="account-details">
			<h3>Edit my account details</h3>
			<div class="form-container">

				<form action="" method="POST">
					<?
					if (isset($this->editAccountError)) {
						?>
						<span class="submission-error"><?= $this->editAccountError ?></span>
						<?
					}
					?>		
					<div class="field">
						<label for="name-name">Update username</label>
						<input type="text" name="new-name" class="text-input" id="new-name" value="<?=$this->userName?>" />
					</div>
					<div class="field">
						<label for="new-email">Update email address</label>
						<input type="email" name="new-email" class="text-input" id="new-email" value="<?=$this->userEmail?>" />
						
					</div>
					<div class="field">
						<label for="new-password">Update password</label>
						<input type="password" name="new-password" class="text-input" id="new-password" value="" />
					</div>
					<div class="field">
						<label for="current-password"><b>Confirm current password</b>*</label>
						<input type="password" name="current-password" class="text-input" id="current-password" value="" />
					</div>
					<div  class="field">
						<input type="hidden" name="submit" value="edit-account" />
						<input type="submit" name="edit-account" value="Save" />
					</div>
				</form>
				<form action="" method="post" class="delete-form" id="delete-reporter-form">
					<input type="hidden" name="submit" value="delete-reporter" />
					<input type="button" id="delete-reporter-btn" class="delete-btn" value="Delete My Account" />
					<div class="overlay" id="delete-btn-overlay" style="display:none;">
						<p>Are you sure you want to delete your account? <strong>All your reports will be deleted</strong></p>
						<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
						<input class="confirm" type="submit" name="delete-location" id="confirm-deletion" value="Confirm"/>
					</div>
				</form>

				<script>
					$('#delete-reporter-btn').click(function(){
						$('#delete-btn-overlay').show();
					});

					$('#delete-btn-overlay #cancel-deletion').click(function(){
						$('#delete-btn-overlay').hide();
					});				
				</script>	
			</div>
		</div>
		<?
	}	

	public function renderRight() {	
		?>
		<div class="location-list sidebar-section">
			<h3>My Locations</h3>
			<? $this->renderMyLocations() ?>	
		</div>		
		<?		
	}
	
	public function renderMyLocations() {
		if ($this->userHasLocations) {
			$options['locations'] = $this->userLocations;
		}
		$options['showAddLocation'] = TRUE;
		$options['showSeeAll'] = TRUE;
		$list = new LocationList($options);
		$list->renderLocations();			
	}	
}
?>