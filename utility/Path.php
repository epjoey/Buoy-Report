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

	public static function toPostReport($locationid = NULL, $submitError = NULL) {
		if (!isset($locationid)) {
			return Path::toLocations(null, true);
		}
		$url = '/location.php?location='.$locationid.'&report=1';
		return $url;
	}

	public static function toSingleReport($reportId) {
		return '/post.php?id='.$reportId;
	}

	public static function toEditReport($reportId, $submitError = NULL) {
		$url = '/edit-post.php?id='.$reportId;
		if (isset($submitError)) {
			$url .= '&error='.$submitError;
		}
		return $url;		
	}	

	public static function toProfile($reporterId, $status = NULL) {	
		$url = '/profile.php?reporter='.$reporterId;
		if (isset($status)) {
			$url .= '&status='.$status;
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

	public static function toSubmitCrew($error = NULL) {
		$url = '/add-crew.php';
		if (isset($error)) {
			$url .= '?error='.$error;
		}
		return $url;			
	}

	public static function toCrew($id = NULL) {
		if (!isset($id)) 
			return self::toSubmitCrew(); //make this go to list of crews
		return '/crew.php?id=' . $id;			
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
		return '/buoys.php';
	}

	public static function toAddBuoy($error = NULL) {
		$str = http_build_query(array('error'=>$error));
		return '/add-buoy.php' . ($str ? "?".$str: "");
	}	

	public static function toEditBuoyPage($id = NULL, $error = NULL) {
		$str = http_build_query(array('id'=>$id, 'error'=>$error));

		return '/edit-buoy.php?' . $str;
	}	

	public static function toImageFile($path, $isRemote = FALSE) {
		if ($isRemote) {
			return $path;
		}
		return $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $path;
	}

	public static function toAbout() {
		return '/about.php';
	}	

	public static function toNOAABuoy($buoyId) {
		return 'http://www.ndbc.noaa.gov/station_page.php?station=' . $buoyId;
	}
	public static function toNOAATideStation($stationId) {
		return 'http://tidesonline.noaa.gov/plotcomp.shtml?station_info=' . $stationId;
	}	
		
//-------------------------------------------------------------------------------//
//-------------------------- CODE DIRECTORIES/FILES -----------------------------//
	
	public static function toControllers() {
		return '/controllers/';
	}
		
	public static function toJs() {
		return '/static/js/';
	}

	public static function toCss() {
		return '/static/css/';
	}

	public static function toImages() {
		return '/static/images/';
	}

	public static function absoluteToImages() {
		global $local_dev;
		if ($local_dev) {
			$url = Path::LOCALURL;
		} 
		else {
			$url = Path::URL;
		}
		$url .= '/static/images/';
		return $url;
	}	

	public static function toMobileImageProcess() {
		global $local_dev;
		return Path::toUrl() . '/controllers/report/mobile-image-process.php';
	}

	public static function to404() {
		return '/controllers/view/404.php';
	}

	//form handlers
	public static function toLocationAddBuoy() {
		return self::toControllers() . 'location/add-buoy.php';
	}
	public static function toLocationRemoveBuoy() {
		return self::toControllers() . 'location/remove-buoy.php';
	}
	public static function toLocationAddTidestation() {
		return self::toControllers() . 'location/add-tidestation.php';
	}
	public static function toLocationRemoveTidestation() {
		return self::toControllers() . 'location/remove-tidestation.php';
	}	
	public static function toDeleteBuoy() {
		return self::toControllers() . 'buoy/delete-buoy.php';
	}
	public static function toEditBuoy() {
		return self::toControllers() . 'buoy/edit-buoy.php';
	}	
	public static function toDeleteTidestation() {
		return self::toControllers() . 'tide/delete-tidestation.php';
	}
	public static function toEditTidestation() {
		return self::toControllers() . 'tide/edit-tidestation.php';
	}	
	public static function toHandleReportSubmission() {
		return self::toControllers() . 'report/report-form-handler.php';
	}
	public static function toHandleEditReportSubmission() {
		return self::toControllers() . 'report/edit-report-form-handler.php';
	}	
	public static function toHandleLogin() {
		return self::toControllers() . 'user/login-form-handler.php';
	}
	public static function toHandleRegistration() {
		return self::toControllers() . 'user/register-form-handler.php';
	}	
	public static function toBookmarkLocation() {
		return self::toControllers() . 'location/add-reporter.php';
	}
	public static function toSetLocationTimezone() {
		return self::toControllers() . 'location/set-timezone.php';
	}	
	public static function toSetLocationName() {
		return self::toControllers() . 'location/set-name.php';
	}		
	public static function toDeleteLocation() {
		return self::toControllers() . 'location/delete-location.php';
	}			
}
?>