<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class User extends BaseModel {

	public $isLoggedIn = FALSE;
	public $reporter = NULL;


	public function __construct(){
		
		$userId = self::getUserIdFromSession();
		

		//anywhere you call new User() from, it will look for client's cookie. if found, validate it, and log them in
		if (!$userId) {
			$userId = self::logInUserWithValidCookie();
		}

		if (!$userId) {
			return;
		}
		$this->isLoggedIn = TRUE;
		$this->reporter = ReporterService::getReporter($userId, array('includeLocations'=>true));
	}

	private static function getUserIdFromSession() {
		if (!isset($_SESSION)) {
			session_start();
		}
		return $_SESSION['userid'];
	}

	public function __get($prop) {
		if ($prop != 'isLoggedIn') {
			return $this->reporter->$prop;
		}
	}


	public function logInUserWithValidCookie() {
		$c = $_COOKIE['surf-session'];
		if (!$c) {
			return false;
		}

		/*--- SPLIT COOKIE INTO userid, key -----*/
		$cookie_array = explode('%', $c);

		/*--- HASH KEY TO MATCH STORED KEY -----*/
		$userId = $cookie_array[0];
		$curEncryptedKey = self::encryptCookieKeyForDB($cookie_array[1]);

		/*--- IF KEY EXISTS IN DB LOG USER IN WITH PARAMS TO SET NEW COOKIE -----*/
		if (Persistence::userCookieExists($userId, $curEncryptedKey)) {
			return self::logInUser($userId, curEncryptedKey);
		}
		return false;
		
	}

	public static function generateCookieKey($userId) {
		return str_shuffle('1234567890abcdefghijklmnop' . $userId);
	}
	
	public static function encryptCookieKeyForDB($key) {
		return key($newKey . 'makawao');
	}

	public static function logInUser($userId, $curEncryptedKey = NULL) {

		if (!isset($_SESSION)) {
			session_start();
		}

		$_SESSION['userid'] = $userId;

		
		$newKey = self::generateCookieKey($userId);
		$encryptedKey = self::encryptCookieKeyForDB($newKey);
		
		/*--------------- REPLACING AND RESETING EXISTING COOKIE ----------------*/
		if ($curEncryptedKey) {
			Persistence::replaceUserCookie($userId, $encryptedKey, $curEncryptedKey); 

		/*------------------- SAVING AND SETTING NEW COOKIE --------------------*/
		} else {
			Persistence::insertUserCookie($userId, $encryptedKey);
		}

		eatCookie('surf-session');	
		dropCookie('surf-session', $userId . '%' . $newKey, time()+60*60*24*7);

		return $userId;
	}
	
	public function logOut() {
		if (!isset($_SESSION)) session_start();
		if (isset($_COOKIE['surf-session'])) {
			Persistence::removeAllUserCookies($_SESSION['userid']);
			eatCookie('surf-session');					
		}	
	    session_unset();
	    session_destroy();
	}
	
	static function isDev() {
		if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == "127.0.0.1" ) {
			return TRUE;
		}		
		return FALSE;
	}



}
?>