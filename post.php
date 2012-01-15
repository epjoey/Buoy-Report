<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/SinglePostPage.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location:'.Paths::to404());
	exit();	
}

$post = new SinglePostPage;
	
$post->loadData($id);
$post->renderPage();
?>