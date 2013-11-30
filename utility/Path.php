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
		$url = '/login';
		if (isset($error)) {
			$url .= '?error=' . $error;
		}
		if (isset($rel)) {
			$url .= isset($error) ? '&' : '?';
			$url .= 'rel=' . $rel;
		}		
		return $url;
	}	

	public static function toRegister($error = NULL) {
		$url = '/signup';
		if (isset($error)) {
			$url .= '?error=' . $error;
		} 
		return $url;
	}

	public static function toPostReport($locationid = NULL, $submitError = NULL) {
		if (!isset($locationid)) {
			return Path::toLocations(null, true);
		}
		$url = '/locations/'.$locationid.'/report';
		return $url;
	}

	public static function toSingleReport($reportId) {
		return '/reports/'.$reportId;
	}

	public static function toEditReport($reportId, $submitError = NULL) {
		$url = '/edit-report/'.$reportId;
		if (isset($submitError)) {
			$url .= '&error='.$submitError;
		}
		return $url;		
	}	

	public static function toProfile($reporterId, $status = NULL) {	
		$url = '/reporters/'.$reporterId;
		return $url;		
	}

	public static function toLocation($locationId = null, $error=NULL) {
		if (!isset($locationId)) {
			return Path::toLocations();
		}
		$url = '/locations/'.$locationId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;
	}

	public static function toLocations($reporterId = null, $toPost = FALSE) {

		$url = '/locations';

		if (isset($reporterId)) {
			$url .= '?reporter=' . $reporterId;
		}
		if ($toPost) { 
			$url .= isset($reporterId) ? '&' : '?' . 'post=true';
		}

		return $url;
	}

	public static function toReporters($locationId = NULL) {
		$url = '/reporters';
		if (isset($locationId)) {
			$url .= '?location=' . $locationId;
		}
		return $url;
	}

	public static function toEditLocation($locationId, $error = NULL) {
		$url = '/edit-location/'.$locationId;
		if (isset($error)) {
			$url .= '&error='.$error;
		}
		return $url;		
	}

	public static function toSubmitLocation($error = NULL) {
		$url = '/add-location';
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

	public static function toBuoys() {	
		return '/buoys';
	}

	public static function toAddBuoy($error = NULL) {
		$str = http_build_query(array('error'=>$error));
		return '/add-buoy' . ($str ? "?".$str: "");
	}	

	public static function toEditBuoyPage($id = NULL, $error = NULL) {
		$str = http_build_query(array('id'=>$id, 'error'=>$error));

		return '/edit-buoy?' . $str;
	}	

	public static function toAbout() {
		return '/about';
	}	
	public static function to404() {
		return '/controllers/page/404.php';
	}
	public static function toMobileImageProcess() {
		return Path::toUrl() . '/controllers/page/mobile-image-process.php';
	}	
	public static function toNOAABuoy($buoyId) {
		return 'http://www.ndbc.noaa.gov/station_page.php?station=' . $buoyId;
	}
	public static function toNOAATideStation($stationId) {
		return 'http://tidesonline.noaa.gov/plotcomp.shtml?station_info=' . $stationId;
	}	
		


//-------------------------------------------------------------------------------//
//-------------------------- CODE DIRECTORIES/FILES -----------------------------//
		
	public static function toJs() {
		return '/view/static/js/';
	}

	public static function toCss() {
		return '/view/static/css/';
	}

	public static function toImages() {
		return '/view/static/images/';
	}


	public static function absoluteToImages() {
		global $local_dev;
		if ($local_dev) {
			$url = Path::LOCALURL;
		} 
		else {
			$url = Path::URL;
		}
		$url .= '/view/static/images/';
		return $url;
	}	

	public static function toImageFile($path, $isRemote = FALSE) {
		if ($isRemote) {
			return $path;
		}
		return $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $path;
	}


	//form handlers
	public static function toLocationAddBuoy() {
		return '/controllers/location/add-buoy.php';
	}
	public static function toLocationRemoveBuoy() {
		return '/controllers/location/remove-buoy.php';
	}
	public static function toLocationAddTidestation() {
		return '/controllers/location/add-tidestation.php';
	}
	public static function toLocationRemoveTidestation() {
		return '/controllers/location/remove-tidestation.php';
	}	
	public static function toDeleteBuoy() {
		return '/controllers/buoy/delete-buoy.php';
	}
	public static function toEditBuoy() {
		return '/controllers/buoy/edit-buoy.php';
	}	
	public static function toDeleteTidestation() {
		return '/controllers/tide/delete-tidestation.php';
	}
	public static function toEditTidestation() {
		return '/controllers/tide/edit-tidestation.php';
	}	
	public static function toHandleReportSubmission() {
		return '/controllers/report/report-form-handler.php';
	}
	public static function toHandleEditReportSubmission() {
		return '/controllers/report/edit-report-form-handler.php';
	}	
	public static function toHandleLogin() {
		return '/controllers/user/login-form-handler.php';
	}
	public static function toLogout() {
		return '/controllers/user/logout.php';
	}
	public static function toHandleRegistration() {
		return '/controllers/user/register-form-handler.php';
	}	
	public static function toBookmarkLocation() {
		return '/controllers/location/add-reporter.php';
	}
	public static function toEditLocationPost() {
		return '/controllers/location/edit.php';
	}	
	public static function toDeleteLocation() {
		return '/controllers/location/delete-location.php';
	}
	public static function toPostLocation() {
		return '/controllers/location/add-location.php';	
	}		
	public static function toUpdateReporter() {
		return '/controllers/reporter/update.php';
	}	
	public static function toDeleteReporter() {
		return '/controllers/reporter/delete.php';
	}
}
?>