<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$id = $_REQUEST['id'];
?>
<iframe src="http://www.ndbc.noaa.gov/widgets/station_page.php?station=<?=$id?>" style="width:100%; min-height: 300px"></iframe>

