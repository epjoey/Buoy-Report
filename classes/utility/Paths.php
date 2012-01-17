<?php

class Paths {
	
	const SHORTURL = 'http://bouyreport.com';
	const URL = 'http://www.bouyreport.com';
	const LOCALURL = 'http://localhost:8888';
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
		return '/';
	}	

	public static function toUserHome() {
		return '/';
	}	

	public static function toLogin($error = NULL) {
		$url = '/login.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		}
		return $url;
	}	

	public static function toLogout() {
		return '/?logout=true';
	}	

	public static function toRegister($error = NULL) {
		$url = '/register.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		} 
		return $url;
	}

	public static function toPostReport($locationid = NULL, $submitError = NULL) {
		if (!isset($locationid)) {
			return Paths::toLocations(null, true);
		}
		$url = '/report.php?location='.$locationid;
		if (isset($submitError)) {
			$url .= '&error='.$submitError;
		}
		return $url;
	}

	public static function toSinglePost($reportId) {
		return '/post.php?id='.$reportId;
	}

	public static function toEditPost($reportId) {
		return '/edit-post.php?id='.$reportId;
	}	

	public static function toProfile($reporterId, $error) {
		if (!isset($reporterId)) {
			return Paths::to404();
		}		
		$url = '/profile.php?reporter='.$reporterId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;		
	}

	public static function toLocation($locationId = null) {
		if (isset($locationId)) {
			return '/location.php?location='.$locationId;
		} else { 
			return Paths::toLocations();
		}
	}

	public static function toLocations($reporterId = null, $toPost = FALSE) {

		$url = '/locations.php';

		if (isset($reporterId)) {
			$url .= '?reporter=' . $reporterId;
		}
		if ($toPost) { 
			$url .= isset($reporterId) ? '&' : '?' . 'post=true';
		}

		return $url;
	}

	public static function toReporters($locationId = null) {
		$url = '/reporters.php';
		if (isset($locationId)) {
			$url .= '?location=' . $locationId;
		}
		return $url;
	}

	public static function toEditLocation($locationId) {
		return '/edit-location.php?location='.$locationId;
	}

	public static function toSubmitLocation() {
		return '/add-location.php';
	}

	public static function toImageSrc($path, $isRemote = FALSE) {
		global $local_dev;
		if ($isRemote) {
			return $path;
		}
		if ($local_dev) {
			return '/uploads/' . $path;
		}
		return Paths::URL . '/uploads/' . $path;
	}

	public static function toImageFile($path, $isRemote = FALSE) {
		if ($isRemote) {
			return $path;
		}
		return $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $path;
	}

	public static function to404() {
		return '/404.php';
	}
		
//-------------------------------------------------------------------------------//
//-------------------------- CODE DIRECTORIES/FILES -----------------------------//
	
	public static function toAjax() {
		return '/ajax/';
	}
		
	public static function toJs() {
		return '/js/';
	}

	public static function toCss() {
		return '/css/';
	}

	public static function toImages() {
		return '/images/';
	}

	public static function absoluteToImages() {
		global $local_dev;

		if ($local_dev) {
			$url = Paths::LOCALURL;
		} 
		else {
			$url = Paths::URL;
		}
		$url .= '/images/';
		return $url;
	}	

	public static function toMobileImageProcess() {
		global $local_dev;

		return Paths::toUrl() . '/utility/mobile-image-process.php';

	}

}
?>