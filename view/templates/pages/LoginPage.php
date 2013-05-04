<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LoginPage extends Page {

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