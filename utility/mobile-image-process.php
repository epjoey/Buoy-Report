<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/MobileImageProcessPage.php';


if (!isset($_SESSION)) session_start();
$location = $_SESSION['location-for-image'];

$page = new MobileImageProcessPage;
$page->loadData($location);
$page->renderPage();
?>
