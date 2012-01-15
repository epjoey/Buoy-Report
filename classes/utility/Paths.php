<?php

class Paths {
	
	const ROOT = '';
	const SHORTURL = 'http://bouyreport.com/';
	const URL = 'http://www.bouyreport.com/';
	const LOCALURL = 'http://localhost:8888/br';
	const COOKIEDOMAIN = '.bouyreport.com';

	public static function toUrl() {
		global $local_dev;

		if ($local_dev) {
			return Paths::LOCALURL;
		} else {
			$urlParts = explode('.', $_SERVER['HTTP_HOST']);
			if ($urlParts[0] == 'www') {
				return Paths::URL;
			} else {
				return Paths::SHORTURL;
			}
		}
	}

	public static function toCookieDomain() {
		global $local_dev;

		if ($local_dev) {
			return "";
		} else {
			return Paths::COOKIEDOMAIN;
		}		
	}

	public static function toIntro() {
		return Paths::ROOT.'/';
	}	

	public static function toUserHome() {
		return Paths::ROOT.'/';
	}	

	public static function toLogin($error = NULL) {
		$url = Paths::ROOT.'/login.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		}
		return $url;
	}	

	public static function toLogout() {
		return Paths::ROOT.'/?logout=true';
	}	

	public static function toRegister($error = NULL) {
		$url = Paths::ROOT.'/register.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		} 
		return $url;
	}

	public static function toPostReport($locationid = NULL, $submitError = NULL) {
		if (!isset($locationid)) {
			return Paths::toLocations(null, true);
		}
		$url = Paths::ROOT.'/report.php?location='.$locationid;
		if (isset($submitError)) {
			$url .= '&error='.$submitError;
		}
		return $url;
	}

	public static function toSinglePost($reportId) {
		return Paths::ROOT.'/post.php?id='.$reportId;
	}

	public static function toEditPost($reportId) {
		return Paths::ROOT.'/edit-post.php?id='.$reportId;
	}	

	public static function toProfile($reporterId) {
		return Paths::ROOT.'/profile.php?reporter='.$reporterId;
	}

	public static function toLocation($locationId = null) {
		if (isset($locationId)) {
			return Paths::ROOT.'/location.php?location='.$locationId;
		} else { 
			return Paths::toLocations();
		}
	}

	public static function toLocations($reporterId = null, $toPost = FALSE) {

		$url = Paths::ROOT.'/locations.php';

		if (isset($reporterId)) {
			$url .= '?reporter=' . $reporterId;
		}
		if ($toPost) { 
			$url .= isset($reporterId) ? '&' : '?' . 'post=true';
		}

		return $url;
	}

	public static function toReporters($locationId = null) {
		$url = Paths::ROOT.'/reporters.php';
		if (isset($locationId)) {
			$url .= '?location=' . $locationId;
		}
		return $url;
	}

	public static function toEditLocation($locationId) {
		return Paths::ROOT.'/edit-location.php?location='.$locationId;
	}

	public static function toSubmitLocation() {
		return Paths::ROOT.'/add-location.php';
	}

	public static function toImageSrc($path, $isRemote = FALSE) {
		global $local_dev;
		if ($isRemote) {
			return $path;
		}
		if ($local_dev) {
			return Paths::ROOT . '/uploads/' . $path;
		}
		return Paths::URL . Paths::ROOT . '/uploads/' . $path;
	}

	public static function toImageFile($path, $isRemote = FALSE) {
		if ($isRemote) {
			return $path;
		}
		return $_SERVER['DOCUMENT_ROOT'] . Paths::ROOT . '/uploads/' . $path;
	}

	public static function to404() {
		return Paths::ROOT.'/404.php';
	}
		
//-------------------------------------------------------------------------------//
//-------------------------- CODE DIRECTORIES/FILES -----------------------------//
	
	public static function toAjax() {
		return Paths::ROOT.'/ajax/';
	}
		
	public static function toJs() {
		return Paths::ROOT.'/js/';
	}

	public static function toCss() {
		return Paths::ROOT.'/css/';
	}

	public static function toImages() {
		return Paths::ROOT.'/images/';
	}

	public static function absoluteToImages() {
		global $local_dev;

		if ($local_dev) {
			$url = Paths::LOCALURL;
		} 
		else {
			$url = Paths::URL;
		}
		$url .= Paths::ROOT.'/images/';
		return $url;
	}	

	public static function toMobileImageProcess() {
		global $local_dev;

		return Paths::toUrl() . Paths::ROOT.'/utility/mobile-image-process.php';
		//return 'http://192.168.1.105:8888/reporter/utility/mobile-image-process.php';

	}

}
?>