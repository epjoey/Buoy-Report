<?php

class Path {
	
	const SHORTURL = 'http://buoy-report.com';
	const URL = 'http://www.buoy-report.com';
	const LOCALURL = 'http://localhost:8888';
	const COOKIEDOMAIN = '.buoy-report.com';

	public static function toUrl() {
		global $local_dev;

		if ($local_dev) {
			return Path::LOCALURL;
		} else {
			$urlParts = explode('.', $_SERVER['HTTP_HOST']);
			if ($urlParts[0] == 'www') {
				return Path::URL;
			} else {
				return Path::SHORTURL;
			}
		}
	}

	public static function toCookieDomain() {
		global $local_dev;

		if ($local_dev) {
			return "";
		} else {
			return Path::COOKIEDOMAIN;
		}		
	}

	public static function toIntro() {
		return '/';
	}	

	public static function toUserHome() {
		return '/';
	}	

	public static function toLogin($error = NULL, $rel = NULL) {
		$url = '/login.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		}
		if (isset($rel)) {
			$url .= isset($error) ? '&' : '?';
			$url .= 'rel=' . $rel;
		}		
		return $url;
	}	

	public static function toHandleLogin() {
		return '/form-handlers/login-form-handler.php';
	}	

	public static function toLogout() {
		return '/logout.php';
	}	

	public static function toRegister($error = NULL) {
		$url = '/register.php';
		if (isset($error)) {
			$url .= '?error=' . $error;
		} 
		return $url;
	}

	public static function toHandleRegistration() {
		return '/form-handlers/register-form-handler.php';
	}

	public static function toPostReport($locationid = NULL, $submitError = NULL) {
		if (!isset($locationid)) {
			return Path::toLocations(null, true);
		}
		$url = '/report.php?location='.$locationid;
		if (isset($submitError)) {
			$url .= '&error='.$submitError;
		}
		return $url;
	}


	public static function toHandleReportSubmission() {
		return '/form-handlers/report-form-handler.php';
	}

	public static function toSinglePost($reportId) {
		return '/post.php?id='.$reportId;
	}

	public static function toEditPost($reportId) {
		return '/edit-post.php?id='.$reportId;
	}	

	public static function toProfile($reporterId, $error = NULL) {
		if (!isset($reporterId)) {
			return Path::to404();
		}		
		$url = '/profile.php?reporter='.$reporterId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;		
	}

	public static function toLocation($locationId = null, $error=NULL) {
		if (!isset($locationId)) {
			return Path::toLocations();
		}
		$url = '/location.php?location='.$locationId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;
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

	public static function toReporters($locationId = NULL) {
		$url = '/reporters.php';
		if (isset($locationId)) {
			$url .= '?location=' . $locationId;
		}
		return $url;
	}

	public static function toEditLocation($locationId, $error = NULL) {
		if (!isset($locationId)) {
			return Path::to404();
		}		
		$url = '/edit-location.php?location='.$locationId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;		
	}

	public static function toSubmitLocation($error = NULL) {
		$url = '/add-location.php';
		if (isset($error)) {
			$url .= '?error='.$error;
		}
		return $url;			
	}

	public static function toImageSrc($path, $isRemote = FALSE) {
		global $local_dev;
		if ($isRemote) {
			return $path;
		}
		if ($local_dev) {
			return '/uploads/' . $path;
		}
		return Path::URL . '/uploads/' . $path;
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

	public static function toAbout() {
		return '/about.php';
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
			$url = Path::LOCALURL;
		} 
		else {
			$url = Path::URL;
		}
		$url .= '/images/';
		return $url;
	}	

	public static function toMobileImageProcess() {
		global $local_dev;

		return Path::toUrl() . '/mobile-image-process.php';

	}

}
?>