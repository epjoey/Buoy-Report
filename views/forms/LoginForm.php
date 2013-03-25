<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LoginForm {
	public function renderForm($logInError = NULL, $rel = NULL) {
		?>
		<div class="form-container <?= isset($logInError) ? 'expanded' : '';?>" id="login-form-container">			
			<form action="<?=Path::toHandleLogin()?>" method="post" id="login-form">
				<? if(isset($logInError)) { ?>
					<span class="submission-error"><?= $logInError ?></span>
				<? } ?>			
				<div class="field">
					<label for="login-username">Username:</label>
					<input class="text-input required username" type="text" name="login-username" id="login-username" placeholder="Enter Username" />
				</div>
				<div class="field">
					<label for="login-password">Password:</label>
					<input class="text-input required" type="password" name="login-password" id="login-password" placeholder="Enter password" />
				</div>
				<div class="field">
					<?
						if (isset($rel)) {
							?>
							<input type="hidden" name="login-rel" value="<?=$rel?>" />
							<?
						}
					?>
					<input type="hidden" name="submit" value="login" />
					<input type="submit" class="submit" name="login" value="Log In" />
				</div>
			</form>
		</div>
		<?
	}
}
?>