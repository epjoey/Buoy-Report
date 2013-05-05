<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$reporterId = $_GET['reporter'];
$reporter = ReporterService::getReporter($reporterId, array('includeLocations' => true));

if (!$reporter) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = UserService::getUser();
$numReportsPerPage = 6;
$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);


//Current user profile
if ($user->isLoggedIn && $reporterId == $user->id) {

	$reportFilters['reporterId'] = $user->id;
	$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
		'start' => 0,
		'limit' => $numReportsPerPage
	));

	$page = new EditProfilePage;
	$page->loadData();
	if (isset($_POST['submit'])) {
		$page->afterSubmit();
		exit();
	}	
	$page->renderPage(array(
		'pageTitle' => 'My Account',
		'reportFilters' => $reportFilters,
		'user' => $user,
		'numReportsPerPage' => $numReportsPerPage,
		'reports' => $reports
	));	


//Reporter profile
} else {
	
	$reportFilters['reporterId'] = $reporterId;
	$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
		'start' => 0,
		'limit' => $numReportsPerPage
	));

	$page = new ProfilePage();
	$page->renderPage(array(
		'reporter' => $reporter,
		'pageTitle' => $reporter->name . '\'s Reporter Profile',
		'reportFilters' => $reportFilters,
		'user' => $user,
		'numReportsPerPage' => $numReportsPerPage,
		'reports' => $reports
	));
}



?>