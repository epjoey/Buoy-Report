<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/EditAccountForm.php';



class EditProfilePage extends GeneralPage {

	private $editAccountError = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'My Account';	
		$this->editAccountForm = new EditAccountForm;

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

	public function getBodyClassName() {
		return 'edit-profile-page';
	}	

	public function afterSubmit() {
		if ($_POST['submit'] == 'edit-account') {
			if (empty($_POST['current-password'])) {
				$error = 1;
				header('Location:'.Path::toProfile($this->user->id, $error));
				exit();				
			}

			$reporterId = Persistence::returnUserId($this->user->email, md5($_POST['current-password'] . 'reportdb'));
			
			if (!isset($reporterId)) {
				$error = 2;
				header('Location:'.Path::toProfile($this->user->id, $error));
				exit();
			}
			if (!empty($_POST['new-password']) && strlen($_POST['new-password']) < 5) {
				$error = 3;
				header('Location:'.Path::toProfile($this->user->id, $error));
				exit();				
			}
			if (!empty($_POST['new-email']) && filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL) != TRUE ) {
				$error = 4;
				header('Location:'.Path::toProfile($this->user->id, $error));
				exit();					
			}

			$options = array();
			if (!empty($_POST['new-email']) && $_POST['new-email'] != $this->user->email) {
				$options['newEmail'] = $_POST['new-email'];
			}
			if (!empty($_POST['new-name']) && $_POST['new-name'] != $this->user->name) {
				$options['newName'] = $_POST['new-name'];
			} 
			if (!empty($_POST['new-password'])) {
				$options['newPassword'] = md5($_POST['new-password'] . 'reportdb');
			}

			if (isset($_POST['report-status']) && $_POST['report-status'] != $this->user->privacySetting) {
				//vardump($_POST['report-status']); exit();
				if ($_POST['report-status'] == '0') {
					Persistence::makeAllUserReportsPrivate($this->user->id);
					$options['privacySetting'] = 0;
				} 
				else if ($_POST['report-status'] == '1') {
					Persistence::makeAllUserReportsPublic($this->user->id);
					$options['privacySetting'] = 1;
				} 				
			} 		
	
			if (count($options) > 0) {
				Persistence::updateUserInfo($this->user->id, $options);
				$this->user->updateUserSession($options);		
			} else {
				$error = 5;
				header('Location:'.Path::toProfile($this->user->id, $error));
				exit();					
			}
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.Path::toProfile($this->user->id));
			exit();		
		}
		
		if ($_POST['submit'] == 'delete-reporter') {
			Persistence::deleteUser($this->user->id);
			header('Location:'.Path::toLogout());
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
				$options['locations'] = $this->user->locations;					
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
		$this->renderEditInfo();
		$this->renderMyReports();
		
	}

	public function renderMyReports() {
		?>
		<div class="reports-container">
			<h3>My Reports</h3>
			<?
			$options['locations'] = $this->user->locations;
			$options['limit'] = 3;	
			$options['on-page'] = 'edit-profile-page';	
			$reports = new ReportFeed;
			$reports->loadData($options);
			$reports->renderFilterIcon();							
			?>
			<div id="report-feed-container" onPage="edit-profile-page">		
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
			<h3>Account Settings</h3>
			<? $this->editAccountForm->renderForm($this->user, $this->editAccountError); ?>
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
		if ($this->user->hasLocations) {
			$options['locations'] = $this->user->locations;
		}
		$options['showAddLocation'] = TRUE;
		$options['showSeeAll'] = TRUE;
		$list = new LocationList($options);
		$list->renderLocations();			
	}	
}
?>