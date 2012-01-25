<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';



class EditProfilePage extends GeneralPage {

	private $editAccountError = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'My Account';	

		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'You must enter your current password to edit account'; break;
				case 2: $e = 'The password you entered is not correct'; break;
				case 3: $e = 'Password must be more than 5 characters'; break;
				case 4: $e = 'You must enter a valid email address'; break;
				case 5: $e = 'No changes were specified'; break;
			}
			$this->editAccountError = $e;
		}	
	}

	public function renderJs(){
		parent::renderJs();
		if (isset($this->editAccountError)) {
			?>
				<script type="text/javascript">
					$(document).ready(function(){
						window.scrollTo(0,document.body.scrollHeight);
					});
				</script>
			<?
		}

	}

	public function afterSubmit() {
		if ($_POST['submit'] == 'edit-account') {
			if (empty($_POST['current-password'])) {
				$error = 1;
				header('Location:'.Paths::toProfile($this->userId, $error));
				exit();				
			}

			$reporterId = Persistence::returnReporterId($this->userEmail, md5($_POST['current-password'] . 'reportdb'));
			
			if (!isset($reporterId)) {
				$error = 2;
				header('Location:'.Paths::toProfile($this->userId, $error));
				exit();
			}
			if (!empty($_POST['new-password']) && strlen($_POST['new-password']) < 5) {
				$error = 3;
				header('Location:'.Paths::toProfile($this->userId, $error));
				exit();				
			}
			if (!empty($_POST['new-email']) && filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL) != TRUE ) {
				$error = 4;
				header('Location:'.Paths::toProfile($this->userId, $error));
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
			if (isset($_POST['report-status']) && $_POST['report-status'] != $this->userInfo['reportStatus']) {
				$options['reportStatus'] = $_POST['report-status'];
			} 		
	
			if (count($options) > 0) {
				Persistence::updateUserInfo($this->userId, $options);
				$this->user->updateUserSession($options);		
			} else {
				$error = 5;
				header('Location:'.Paths::toProfile($this->userId, $error));
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
		$this->renderEditInfo($this->editAccountError);
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
						<span class="submission-error"><?= html($this->editAccountError) ?></span>
						<?
					}
					?>		
					<div class="field">
						<label for="name-name">Update username</label>
						<input type="text" name="new-name" class="text-input" id="new-name" value="<?=html($this->userName)?>" />
					</div>
					<div class="field">
						<label for="new-email">Update email address</label>
						<input type="email" name="new-email" class="text-input" id="new-email" value="<?=html($this->userEmail)?>" />
						
					</div>
					<div class="field">
						<label for="new-password">Update password</label>
						<input type="password" name="new-password" class="text-input" id="new-password" value="" />
					</div>
					<? /*
					<div  class="field radio-menu">
						<label for="reports-public">My Reports are</label>
						<div class="radio-container">
							<span class="radio-field">
								<input type="radio" class="required" name="report-status" id="public-status" value="1" <?= 
									$this->userInfo['reportStatus'] == 1 ? "checked = 'true'" : ""; 
								?>/><label for="public-status"> Public</label>
							</span>
							<span class="radio-field">
								<input type="radio" class="required" name="report-status" id="private-status" value="0" <?=
									$this->userInfo['reportStatus'] == 0 ? "checked = 'true'" : ""; 
								?>/><label for="private-status"> Private</label>
							</span>		
						</div>				
					</div>	
					*/ ?>					
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
						window.scrollTo(0,0);
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
		<div class="location-list">
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