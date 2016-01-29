<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();
if ($user->isLoggedIn) {
  header('Location:'.Path::toReports($user->id));
  exit();
} else {
  $locations = LocationService::getAllLocations();
  $page = new IntroPage();
  $page->renderPage(array(
  	'pageTitle' => 'Welcome',
  	'user' => $user,
  	'detect' => new Mobile_Detect(),
  	'locations' => $locations
  ));
}
//   exit();
// }


// // For logged in folks, show their reports
// $reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
// $reportFilters['locationIds'] = Utils::pluck($user->locations, 'id');
// $numReportsPerPage = 6;
// $reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
// 	'start' => 0,
// 	'limit' => $numReportsPerPage
// ));
// $page = new ReportsPage();
// $page->renderPage(array(
// 	'pageTitle' => 'Home',
// 	'user' => $user,
// 	'numReportsPerPage' => $numReportsPerPage,
// 	'reports' => $reports,
// 	'reportFilters' => $reportFilters
// ));
// 
?>