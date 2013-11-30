<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

interface Interfacial {}

class UserService implements Interfacial {

	static $USER;
	static $COOKIE_NAME = 'surf-session';

	public static function getUser() {
		if (!isset(self::$USER)) {
			self::$USER = new User();
		}
		return self::$USER;
	}

	public static function logInUser($name, $pw) {
		$reporter = ReporterService::getReporterByUsernameAndPassword($name, $pw);
		if (!$reporter) {
			throw new InvalidSubmissionException('The specified username or password is incorrect');
		}
		User::logIn($reporter->id);
	}

	public static function logOutUser() {
		$user = self::getUser();
		$userId = intval($user->id);
		Persistence::run("DELETE FROM usercookie WHERE userid = '$userId'");
		eatCookie(self::$COOKIE_NAME);					
		session_unset();
		session_destroy();
	}
	
	static function isDev() {
		return $_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == "127.0.0.1";
	}	

}
?>