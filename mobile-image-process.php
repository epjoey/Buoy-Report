<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new MobileImageProcessPage;
$page->loadData();
$page->renderPage();
?>
