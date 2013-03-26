<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class UserService {

	static $USER;

	public static function getUser() {
		if (!isset(self::$USER)) {
			self::$USER = new User();
		}
		return self::$USER;
	}

	public static function logOutUser() {
		self::getUser()->logOut();
	}

}
?>