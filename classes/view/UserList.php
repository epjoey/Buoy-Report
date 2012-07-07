<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';


class UserList {
	
	public function renderUserList() { 
		?>
		<ul>
			<? foreach (Persistence::getUsers() as $user): ?>
				<li>
					<a href="<?=Path::toProfile($user['id']);?>"><?= isset($user['name']) ? html($user['name']) : html($user['email']);?></a>
				</li>
			<? endforeach; ?>
		</ul>
		<?		
	}
}
?>