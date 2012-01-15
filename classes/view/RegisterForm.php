<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

class RegisterForm {
	public function renderForm($registerError = NULL) {	
		?>

		<div class="form-container <?= isset($registerError) ? 'expanded' : '';?>" id="reg-form-container">			
			<form action="<?=Paths::toRegister()?>" method="post" id="reg-form">
				<? if(isset($registerError)) { ?>
					<span class="submission-error"><?= $registerError ?></span>
				<? } ?>				
				<div class="field">
					<label for="reg-name">Choose a Username:</label>
					<input class="text-input required username" type="text" name="reg-name" id="reg-name" placeholder="Choose a username"/>
				</div>		
				<div class="field">
					<label for="reg-email">Your Email Address:</label>
					<input class="text-input required email" type="email" name="reg-email" id="reg-email" placeholder="joe@example.com" />
				</div>
				<div class="field">
					<label for="reg-password">Choose a Password:</label>
					<input class="text-input required password" type="password" name="reg-password" id="reg-password" placeholder="••••••••••" />
				</div>
				<div class="field">
					<input type="hidden" name="submit" value="register" />
					<input type="submit" name="register" value="Sign Up" />
				</div>
			</form>
		</div>
		<?
	}
}
?>