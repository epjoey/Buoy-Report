<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';

class UserService {
	static function getCurrentUser() {
		return new User();
	}
}
?>