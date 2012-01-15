<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

class LogoutForm {
	public function renderForm() { ?>
		<form action="<?=Paths::toIntro() ?>" method="post" class="log-out-form">
			<input type="hidden" name="submit" value="logout" />
			<input type="submit" value="Log Out" />
		</form>
	<?
	}
}
?>