<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/SingleReportPage.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location:'.Path::to404());
	exit();	
}

$post = new SingleReportPage;
	
$post->loadData($id);
$post->renderPage();
?>