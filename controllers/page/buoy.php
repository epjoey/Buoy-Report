<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';

$buoyId = $_GET['buoyid'];
if (!$buoyId) {
	header('Location:'.Path::toBuoys());
	exit();
}
$buoy = BuoyService::getBuoy($buoyId);

if (!$buoy) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = UserService::getUser();

/* load Report Filters */
$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
$reportFilters['buoyIds'] = array($buoy->id);

/* load Reports */
$numReportsPerPage = 6;
$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'start' => 0,
	'limit' => $numReportsPerPage
));

$page = new BuoyDetailPage();
$page->renderPage(array(
	'pageTitle' => $buoy->name,
	'user' => $user,
	'buoy' => $buoy,
	'reportFilters' => $reportFilters,
	'numReportsPerPage' => $numReportsPerPage,
	'reports' => $reports,
));
?>