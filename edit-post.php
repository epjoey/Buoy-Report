<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header('Location:'.Path::to404());
	exit();	
}

$post = new EditPostPage;
	
$post->loadData($id);
$post->renderPage();
?>