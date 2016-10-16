<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */

$locationid = $_POST['locationid'];

if (!$locationid) {
  throw new Error('Location ID required to post report');
}

try { 

  $user = UserService::getUser();

  if($user->isLoggedIn){
  	$userid = $user->id;
  	$public = $user->public;
  } else {
  	$userid = null;
  	$public = true;
  }


  if(isset($_POST['sublocationid'])) {
    $locationid = $_POST['sublocationid'];
  }

  $location = LocationService::getLocation($locationid, array(
    //'includeSublocations' => true,
    'includeBuoys' => true,
    'includeTideStations' => true
  ));

  if ($location->parentLocationId) {
    $location->parentLocation = LocationService::getLocation($location->parentLocationId, array(
      'includeBuoys' => true,
      'includeTideStations' => true
    ));
    $location->tideStations = array_merge($location->tideStations, $location->parentLocation->tideStations);
    $location->buoys = array_merge($location->buoys, $location->parentLocation->buoys);
  }

  /* either real date or date offset passed in from form */
  if (isset($_POST['time']) && $_POST['time']) {
    $reportDate = new DateTime($_POST['time'], new DateTimeZone($location->timezone));
    if (!$reportDate) {
      throw new InvalidSubmissionException('Date must be valid format');
    }
    if ($reportDate->getTimestamp() > time()) {
      throw new InvalidSubmissionException('Date must be in the past'); 
    }
    $obsdate = gmdate("U", $reportDate->getTimestamp());
  } else {
    $offset = abs(intval($_POST['time_offset'])) * 60 * 60; //offset is submitted in hours
    $obsdate = gmdate("U", time()-$offset);     
  }

  $imagepath = null;
  if (isset($_POST['imageurl']) && $_POST['imageurl'] !='') {
    $imagepath = rawurldecode($_POST['imageurl']);
  } 

  $quality = null;
  if (isset($_POST['quality'])) {
  	$quality = $_POST['quality'];
  }

  $text = null;
  if(isset($_POST['text'])) {
  	$text = $_POST['text'];
  }

  $waveheight = null;
  if(isset($_POST['waveheight'])) {
  	$waveheight = $_POST['waveheight'];
  }

  $reportId = ReportService::insertReport(array(
    'quality' => $quality,
    'obsdate' => $obsdate,
    'reporterid' => $userid,
    'public' => $public,
    'locationid' => $location->id,
    'text' => $text,
    'waveheight' => $waveheight,
    'imagepath' => $imagepath
  ));

  //fetch and insert tide data for submitted tide stations
  foreach($location->tideStations as $tideStation) {
    $tideReport = TideReportService::getTideStationTideReport($tideStation, array('time'=>$obsdate));
    if ($tideReport) {
      $tideReport->reportid = $reportId;  
      TideReportService::insertTideReport($tideReport);
    }
  }

  //fetch and insert buoy data for submitted tide stations
  foreach($location->buoys as $buoy) {
    $buoyReports = BuoyReportService::getBuoyReports($buoy->buoyid, array(
      'offset'=>$obsdate,
      'limit'=>1 //only want one report
    ));
    if ($buoyReports) {
      $buoyReport = $buoyReports[0]; //only want one report
      $buoyReport->reportid = $reportId;  
      BuoyReportService::insertBuoyReport($buoyReport);
    }
  }

  //add location to reporter's locations
  //ReporterService::reporterAddLocation($user->id, $_POST['locationid']);  

} catch (InvalidSubmissionException $e) {
  StatusMessageService::setStatusMsgForAction($e->getMessage(), 'submit-report-form');
  header('Location:'.Path::toPostReport($location->id));
  exit;

}
header('Location:'.Path::toSingleReport($reportId));
?>