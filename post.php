<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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