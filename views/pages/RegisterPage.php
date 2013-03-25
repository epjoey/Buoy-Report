<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class RegisterPage extends Page {

	private $registerError = NULL;

	public function loadData(){
		parent::loadData();
		$this->pageTitle = 'Sign Up';	
		if (isset($_GET['error']) && $_GET['error']) {
			switch($_GET['error']) {
				case 1: $e = 'Please fill in all fields'; break;
				case 2: $e = 'You must enter a valid email address'; break;
				case 3: $e = 'An account with that email already exists.'; break;
				case 4: $e = 'An account with that username already exists.'; break;
				case 5: $e = 'Password must contain at least 5 characters.'; break;
				case 6: $e = 'Your not human!';
			}
			$this->registerError = $e;
		}	
	}
	
	public function getBodyClassName() {
		return 'reg-page';
	}	

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#reg-name').focus();
				$("#reg-form").validate({
					rules: {
					    "reg-password": {
					        required: true,
					        minlength: 5
					    },
					    "reg-email": {
					        required: true,
					        email: true
					    }					    
					}
				});
			});
		</script>
		<?
	}	

	public function renderBodyContent() {
		?>
		<h1 class="form-head">Sign Up</h1>	
		<?
		$reg = new RegisterForm;
		$reg->renderForm($this->registerError);
	}
}



?>