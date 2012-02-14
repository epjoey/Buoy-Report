<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/MobileImageProcessPage.php';

$page = new MobileImageProcessPage;
$page->loadData();
$page->renderPage();
?>
