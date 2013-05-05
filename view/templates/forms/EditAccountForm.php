<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditAccountForm {

	public static function renderForm($user, $editAccountStatus = NULL) {	
		?>
		<div class="form-container">

			<form action="" method="POST">
				<?
				if (isset($editAccountStatus)) {
					?>
					<span class="submission-error"><?= html($editAccountStatus) ?></span>
					<?
				}
				?>		
				<div class="field">
					<label for="name-name">Update username</label>
					<input type="text" name="new-name" class="text-input" id="new-name" value="<?=html($user->name)?>" />
				</div>
				<div class="field">
					<label for="new-email">Update email address</label>
					<input type="email" name="new-email" class="text-input" id="new-email" value="<?=html($user->email)?>" />
					
				</div>
				<div class="field">
					<label for="new-password">Update password</label>
					<input type="password" name="new-password" class="text-input" id="new-password" placeholder="••••••••••" value="" />
				</div>
				
				<? /* Privacy Settings */ ?>
				<div class="field radio-menu privacy-settings">
					<label for="reports-public">Privacy Settings</label>
					<div class="radio-container">
						<span class="radio-field">
							<input type="radio" class="required" name="report-status" id="public-status" value="1" <?= 
								$user->public == 1 ? "checked = 'true'" : ""; 
							?>/><label for="public-status"> My reports are public</label>
						</span>
						<span class="radio-field">
							<input type="radio" class="required" name="report-status" id="private-status" value="0" <?=
								$user->public == 0 ? "checked = 'true'" : ""; 
							?>/><label for="private-status"> My reports are private</label>
						</span>	
						<? /*
						<span class="radio-field">
							<input type="radio" class="required" name="report-status" id="public-status-all" value="all-public" /><label for="public-status-all"> Make all (past &amp; future) reports public</label>
						</span>
						<span class="radio-field">
							<input type="radio" class="required" name="report-status" id="private-status-all" value="all-private" /><label for="private-status-all"> Make all (past &amp; future) reports private</label>
						</span>	
						*/ ?>								
					</div>				
				</div>	
				
				<div class="field">
					<label for="current-password"><b>Confirm current password</b>*</label>
					<input type="password" name="current-password" class="text-input" id="current-password" placeholder="••••••••••" value="" />
				</div>
				<div  class="field">
					<input type="hidden" name="submit" value="edit-account" />
					<input type="submit" name="edit-account" value="Save" />
				</div>
			</form>
			<form action="" method="post" class="delete-form" id="delete-reporter-form">
				<input type="hidden" name="submit" value="delete-reporter" />
				<input type="button" id="delete-reporter-btn" class="delete-btn" value="Delete My Account" />
				<div class="overlay" id="delete-btn-overlay" style="display:none;">
					<p>Are you sure you want to delete your account? <strong>All your reports will be deleted!</strong></p>
					<input type="button" class="cancel" id="cancel-deletion" value="Cancel"/>
					<input class="confirm" type="submit" name="delete-location" id="confirm-deletion" value="Confirm"/>
				</div>
			</form>

			<script>
				$('#delete-reporter-btn').click(function(){
					$('#delete-btn-overlay').show();
					window.scrollTo(0,0);
				});

				$('#delete-btn-overlay #cancel-deletion').click(function(){
					$('#delete-btn-overlay').hide();
				});				
			</script>	
		</div>
		<?
	}
}
?>