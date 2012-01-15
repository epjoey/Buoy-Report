<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';


if (!isset($_SESSION)) session_start();

$options['on-page'] = $_GET['on-page'];
if ($options['on-page'] == 'homepage') {
	$options['locations'] = Persistence::getUserLocations($_SESSION['userid']);
}
if ($options['on-page'] == 'location-page') {
	$options['locations'] = array(Persistence::getLocationInfoById($_GET['location']));
}
if ($options['on-page'] == 'profile-page' || $options['on-page'] == 'edit-profile-page') {
	$options['locations'] = Persistence::getUserLocations($_GET['reporter']);
}
$reports = new ReportFeed;
$reports->loadData($options);
$reports->renderReportFeed();
?>
