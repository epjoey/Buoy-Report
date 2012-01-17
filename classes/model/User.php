<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';

class User {

	public $loginError = NULL;
	public $registerError = NULL;

	//handles login/logouts and grants user with session access
	public function userIsLoggedIn() {
		/* --------------- HANDLE LOGOUT FORM SUBMISSION --------------- */
		if ((isset($_REQUEST['logout']) && $_REQUEST['logout']) || (isset($_POST['submit']) && $_POST['submit'] == 'logout')) {
			$this->logOutUser();
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::toIntro());
			exit();
		}		

		/* ----------------- CHECK IF USER HAS SESSION ----------------- */
		if (!isset($_SESSION)) session_start();
		if (isset($_SESSION['userid']) && $_SESSION['userid'] != '')
			return TRUE;		

		/* ----------------- CHECK IF USER HAS COOKIE ----------------- */
		if (isset($_COOKIE['surf-session']))
			return $this->handleCookie();

		return FALSE;
	}

	public function handleLoginFormSubmission() {
		if (!isset($_POST['login-email']) || $_POST['login-email'] == '' || !isset($_POST['login-password']) || $_POST['login-password'] == '') {
			$error = 1;
		}
			
		else {
			$reporterId = Persistence::returnReporterId($_POST['login-email'], md5($_POST['login-password'] . 'reportdb'));
			if (!isset($reporterId)) {
				$error = 2;	
			}
		}

		if (isset($error)) {
			header('Location:'.Paths::toLogin($error));
			exit();
			
		} else {		
			$this->logInUser($reporterId, NULL, $newCookie = TRUE);
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::toUserHome());
			exit();
		}		
	}
	
	//registers new user and signs them in
	public function handleRegFormSubmission() {
		
		/* --------------- HANDLE SIGNUP FORM SUBMISSION --------------- */
	
		if (!isset($_POST['reg-name']) || $_POST['reg-name'] == '' || !isset($_POST['reg-email']) || $_POST['reg-email'] == '' || !isset($_POST['reg-password']) || $_POST['reg-password'] == '') {
			$error = 1;
		}
		else if (filter_var($_POST['reg-email'], FILTER_VALIDATE_EMAIL) != TRUE) {
			$error = 2;
		}
		else if (strlen($_POST['reg-password']) < 5) {
			$error = 5;
		} 		
		else if (Persistence::databaseContainsEmail($_POST['reg-email'])) {
			$error = 3;
		}
		else if (Persistence::databaseContainsName($_POST['reg-name'])) {
			$error = 4;
		}

		if (isset($error)) {
			header('Location:'.Paths::toRegister($error));
			exit();
			
		} else {		
			$reporterId = Persistence::insertReporter($_POST['reg-name'], $_POST['reg-email'], md5($_POST['reg-password'] . 'reportdb'));
			$this->logInUser($reporterId, NULL, $newCookie = TRUE, $fromRegistration = TRUE);
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::toUserHome());
			exit();
		}
	}	

	public function handleCookie() {

		/*--- SPLIT COOKIE INTO userid, key -----*/
		$cookie_array = explode('%', $_COOKIE['surf-session']);

		/*--- HASH KEY TO MATCH STORED KEY -----*/
		$userId = $cookie_array[0];
		$curCookieKey = md5($cookie_array[1] . 'makawao');

		/*--- IF KEY EXISTS IN DB LOG USER IN WITH PARAMS TO SET NEW COOKIE -----*/
		if (!Persistence::userCookieExists($userId, $curCookieKey)) {
			 return FALSE;
		} else {
			$this->logInUser($userId, $curCookieKey, $newCookie = TRUE);
			return TRUE;
		}		
		
	}
	
	public function logInUser($userId, $curCookieKey = NULL, $newCookie = TRUE, $fromRegistration = FALSE) {
		$reporterInfo = Persistence::getReporterInfoById($userId);

		if (!isset($_SESSION))	
			session_start();

		$_SESSION['userid'] = $reporterInfo['id'];
		$_SESSION['email'] = $reporterInfo['email'];
		$_SESSION['name'] = $reporterInfo['name'];
		$_SESSION['joindate'] = $reporterInfo['joindate'];
		$_SESSION['justRegistered'] = $fromRegistration;
		$_SESSION['reportStatus'] = $reporterInfo['public'];

		
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
	
	public function getCurrentUser() {
		if (!isset($_SESSION)) session_start();
		$this->userEmail = $_SESSION['email'];
		$this->userId = $_SESSION['userid'];
		$this->userName = $_SESSION['name'];
		$this->userJoinDate = $_SESSION['joindate'];
		$this->userJustRegistered = $_SESSION['justRegistered'];
		$this->reportStatus = $_SESSION['reportStatus'];
		if (isset($_SESSION['new-report'])) {
			$this->newReport = $_SESSION['new-report'];
		} else {
			$this->newReport = NULL;
		}
	}

	public function updateUserSession($options = array()) {
		if (!isset($_SESSION)) session_start();

		if (isset($options['newEmail'])) {
			$_SESSION['email'] = $options['newEmail'];
		}
		if (isset($options['newName'])) {			
			$_SESSION['name'] = $options['newName'];
		}
		if (isset($options['reportStatus'])) {			
			$_SESSION['reportStatus'] = $options['reportStatus'];
		}		
	}

	public static function unsetNewReport() {
		if (!isset($_SESSION)) session_start();
		unset ($_SESSION['new-report']);
	}
}
?>