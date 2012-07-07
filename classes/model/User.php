<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';

class User {

	public $id = NULL;
	public $name = NULL;
	public $email = NULL;
	public $password = NULL;
	public $isLoggedIn = FALSE;
	public $newReport = NULL;
	public $hasNewReport = NULL;
	public $privacySetting = NULL;
	public $isNew = FALSE;
	public $joinDate = NULL;
	public $locations = NULL;	
	public $hasLocations = FALSE;	
	public $loginError = NULL;
	public $registerError = NULL;


	public function __construct(){

		if ($this->userIsLoggedIn()) {
			$this->isLoggedIn = true;				
			$this->email = $_SESSION['email'];
			$this->id = $_SESSION['userid'];
			$this->name = $_SESSION['name'];
			$this->joinDate = $_SESSION['joindate'];
			$this->isNew = $_SESSION['justRegistered'];
			$this->privacySetting = $_SESSION['privacySetting'];
			if (isset($_SESSION['new-report'])) {
				$this->newReport = $_SESSION['new-report'];
				$this->hasNewReport = TRUE;
			}			
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
			return $this->handleCookie();
		}
		return FALSE;
	}

	public function handleCookie() {

		/*--- SPLIT COOKIE INTO userid, key -----*/
		$cookie_array = explode('%', $_COOKIE['surf-session']);

		/*--- HASH KEY TO MATCH STORED KEY -----*/
		$userId = $cookie_array[0];
		$curCookieKey = md5($cookie_array[1] . '');

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
			Persistence::replaceUserCookie($userId, md5($newKey . ''), $curCookieKey); 
			eatCookie('surf-session');	
			dropCookie('surf-session', $userId . '%' . $newKey, time()+60*60*24*7);
		}
		
		/*------------------- SAVING AND SETTING NEW COOKIE --------------------*/
		else if ($newCookie) {
			$newKey = str_shuffle('1234567890abcdefghijklmnop' . $userId); 	
			Persistence::insertUserCookie($userId, md5($newKey . ''));
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

	public static function unsetNewReport() {
		if (!isset($_SESSION)) session_start();
		unset ($_SESSION['new-report']);
	}

	/* this should go in a UserLocation class */
	public function getUserLocations($userId){
		$this->locations = Persistence::getUserLocations($userId);
		if (!empty($this->locations)) {
			$this->hasLocations = TRUE;		
		}		

		/* 
		 * Squish the new report info into 
		 * the locations array before new 
		 * report is submitted into DB.
		 * 
		 */
		if ($this->hasNewReport) {

			if ($this->newReport['reporterHasLocation'] == '0') {
				array_unshift(
					$this->locations, 
					array(
						'id'=>$this->newReport['locationid'], 
						'locname'=>$this->newReport['locationname']
					)
				);
			}
		}	
		if ($this->hasLocations) {
			foreach($this->locations as $location) {
				$this->locationIds[] = $location['id'];
			}		
		}
		
	}	
}
?>