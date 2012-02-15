<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

class LogoutForm {
	public function renderForm() { ?>
		<form action="<?=Path::toIntro() ?>" method="post" class="log-out-form">
			<input type="hidden" name="submit" value="logout" />
			<input type="submit" value="Log Out" />
		</form>
	<?
	}
}
?>