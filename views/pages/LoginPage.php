<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LoginPage extends Page {

	private $loginError = NULL;
	private $loginRel = NULL;

	public function loadData(){
		parent::loadData();
		$this->pageTitle = 'Login';	
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Please fill in both fields'; break;
				case 2: $e = 'The specified username or password was incorrect.';
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
				$('#login-username').focus();
				$("#login-form").validate();
			});
		</script>
		<?
	}	

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Log In</h1>
		<?	
		LoginForm::renderForm($this->loginError, $this->loginRel);
		?>
			<p class="need-account">Need an account? <a href="<?=Path::toRegister();?>">Sign up!</a></p>
		<?	
	}
}



?>