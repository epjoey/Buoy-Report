<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/MobileImageProcessPage.php';


if (!isset($_SESSION)) session_start();
$location = $_SESSION['location-for-image'];

$page = new MobileImageProcessPage;
$page->loadData($location);
$page->renderPage();
?>
