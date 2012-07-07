<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FormHandlers.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */


try {
	
	$post = FormHandlers::handleReportForm($_POST, $_FILES);

} catch(Exception $e) {
	//print $e->getMessage();
	header('Location:'.Path::toPostReport($_POST['locationid'], $e->getMessage()));
	exit;

}


/* Storing report in session */			
if (!isset($_SESSION)) session_start();
$_SESSION['new-report'] = $post; 

/* redirect to user home page where page will look for session[new-report] and load via ajax. */
header('Location:'.Path::toUserHome());
?>