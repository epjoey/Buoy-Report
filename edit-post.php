<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditPostPage.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header('Location:'.Path::to404());
	exit();	
}

$post = new EditPostPage;
	
$post->loadData($id);
if (isset($_POST['submit'])) {
	$post->afterSubmit();
}
$post->renderPage();
?>