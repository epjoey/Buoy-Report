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

	public static function logInUser($name, $pw) {
		$reporter = ReporterService::getReporterByUsernameAndPassword($name, $pw);
		if (!$reporter) {
			throw new InvalidSubmissionException('The specified username or password is incorrect');
		}
		User::logIn($reporter->id);
	}

}
?>