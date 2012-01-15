<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/Persistence.php';


class UserList {
	
	public function renderUserList() { 
		?>
		<ul>
			<? foreach (Persistence::getReporters() as $reporter): ?>
				<li>
					<a href="<?=Paths::toProfile($reporter['id']);?>"><?= isset($reporter['name']) ? html($reporter['name']) : html($reporter['email']);?></a>
				</li>
			<? endforeach; ?>
		</ul>
		<?		
	}
}
?>