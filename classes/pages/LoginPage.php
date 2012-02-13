<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LoginForm.php';

class LoginPage extends GeneralPage {

	private $loginError = NULL;
	private $loginRel = NULL;

	public function loadData(){
		parent::loadData();
		$this->pageTitle = $this->siteTitle . ' Login';	
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Please fill in both fields'; break;
				case 2: $e = 'The specified email address or password was incorrect.';
			}
			$this->loginError = $e;
		}		
		if (isset($_GET['rel']) && $_GET['rel'] != '') {
			$this->loginRel = $_GET['rel'];
		}			
	}

	public function getBodyClassName() {
		return 'login-page';
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#login-email').focus();
				$("#login-form").validate();
			});
		</script>
		<?
	}	

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Log In</h1>
		<?	
		$login = new LoginForm;	
		$login->renderForm($this->loginError, $this->loginRel);
		?>
			<p class="need-account">Need an account? <a href="<?=Paths::toRegister();?>">Sign up!</a></p>
		<?	
	}
}



?>