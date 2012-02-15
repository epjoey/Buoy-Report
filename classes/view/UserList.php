<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';


class UserList {
	
	public function renderUserList() { 
		?>
		<ul>
			<? foreach (Persistence::getReporters() as $reporter): ?>
				<li>
					<a href="<?=Path::toProfile($reporter['id']);?>"><?= isset($reporter['name']) ? html($reporter['name']) : html($reporter['email']);?></a>
				</li>
			<? endforeach; ?>
		</ul>
		<?		
	}
}
?>