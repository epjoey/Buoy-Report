<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class RegisterPage extends Page {
	
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
		RegisterForm::renderForm($this->registerError);
	}
}



?>