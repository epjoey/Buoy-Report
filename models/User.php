<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class User extends Reporter {

	public $isLoggedIn = FALSE;
	public $privacySetting = NULL;
	public $isNew = FALSE;

	public $locations = null; //array of Location models


	public function __construct(){

		//this function does alot. sorry.
		$this->isLoggedIn = $this->userIsLoggedIn();

		if (!$this->isLoggedIn) {
			return;
		}
	
		$this->email = $_SESSION['email'];
		$this->id = $_SESSION['userid'];
		$this->name = $_SESSION['name'];
		$this->joinDate = $_SESSION['joindate'];
		$this->isNew = $_SESSION['justRegistered'];
		$this->privacySetting = $_SESSION['privacySetting'];	


		//todo: move into ReporterService::getLoggedInReporter or something

		if (!isset($this->locations)) {
			$this->locations = LocationService::getReporterLocations($this);
		}
		
	}


	//handles login/logouts and grants user with session access
	public function userIsLoggedIn() {
		/* ----------------- CHECK IF USER HAS SESSION ----------------- */
		if (!isset($_SESSION)) session_start();
		if (isset($_SESSION['userid']) && $_SESSION['userid'] != '') {
			return TRUE;		
		}

		/* ----------------- CHECK IF USER HAS COOKIE ----------------- */
		if (isset($_COOKIE['surf-session'])) {
			return $this->isCookieValid();
		}
		return FALSE;
	}

	public function isCookieValid() {

		/*--- SPLIT COOKIE INTO userid, key -----*/
		$cookie_array = explode('%', $_COOKIE['surf-session']);

		/*--- HASH KEY TO MATCH STORED KEY -----*/
		$userId = $cookie_array[0];
		$curCookieKey = md5($cookie_array[1] . 'makawao');

		/*--- IF KEY EXISTS IN DB LOG USER IN WITH PARAMS TO SET NEW COOKIE -----*/
		if (!Persistence::userCookieExists($userId, $curCookieKey)) {
			 return FALSE;
		} else {
			self::logInUser($userId, $curCookieKey, $newCookie = TRUE);
			return TRUE;
		}		
		
	}
	
	public static function logInUser($userId, $curCookieKey = NULL, $newCookie = TRUE, $fromRegistration = FALSE) {

		$reporterInfo = Persistence::getUserInfoById($userId);

		if (!isset($_SESSION))	
			session_start();

		$_SESSION['userid'] = $reporterInfo['id'];
		$_SESSION['email'] = $reporterInfo['email'];
		$_SESSION['name'] = $reporterInfo['name'];
		$_SESSION['joindate'] = $reporterInfo['joindate'];
		$_SESSION['justRegistered'] = $fromRegistration;
		$_SESSION['privacySetting'] = $reporterInfo['public']; //should rename db table

		
		/*--------------- REPLACING AND RESETING EXISTING COOKIE ----------------*/
		if (isset($curCookieKey) && $newCookie) {
			$newKey = str_shuffle('1234567890abcdefghijklmnop' . $userId);
			Persistence::replaceUserCookie($userId, md5($newKey . 'makawao'), $curCookieKey); 
			eatCookie('surf-session');	
			dropCookie('surf-session', $userId . '%' . $newKey, time()+60*60*24*7);
		}
		
		/*------------------- SAVING AND SETTING NEW COOKIE --------------------*/
		else if ($newCookie) {
			$newKey = str_shuffle('1234567890abcdefghijklmnop' . $userId); 	
			Persistence::insertUserCookie($userId, md5($newKey . 'makawao'));
			dropCookie('surf-session', $userId . '%' . $newKey, time()+60*60*24*7);
		}
	}
	
	public function logOutUser() {
		if (!isset($_SESSION)) session_start();
		if (isset($_COOKIE['surf-session'])) {
			Persistence::removeAllUserCookies($_SESSION['userid']);
			eatCookie('surf-session');					
		}	
	    session_unset();
	    session_destroy();
	}
	

	public function updateUserSession($options = array()) {
		if (!isset($_SESSION)) session_start();

		
		if (isset($options['newEmail'])) {
			$_SESSION['email'] = $options['newEmail'];
		}
		
		if (isset($options['newName'])) {			
			$_SESSION['name'] = $options['newName'];
		}
		if (isset($options['privacySetting'])) {			
			$_SESSION['privacySetting'] = $options['privacySetting'];
		}		
	}

	/* GLOBALS */
	static function isDev() {
		if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == "127.0.0.1" ) {
			return TRUE;
		}		
		return FALSE;
	}



}
?>