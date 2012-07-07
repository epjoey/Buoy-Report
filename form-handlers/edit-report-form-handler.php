<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FormHandlers.php';

/* --------------- HANDLE EDIT REPORT FORM SUBMISSION --------------- */

if ($_POST['submit'] == 'delete-report') {
	Persistence::deleteReport($_POST['id']);
	header('Location:'.Path::toUserHome());
	exit();	
}


try {
	
	$post = FormHandlers::handleReportForm($_POST, $_FILES);

} catch(Exception $e) {
	//print $e->getMessage();
	header('Location:'.Path::toEditPost($_POST['id'], $e->getMessage()));
	exit;

}


Persistence::updateReport($post);
header('Location:'.Path::toSinglePost($post['id']));
?>