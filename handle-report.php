<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

$newReport = new Report;

//theres an error with the submission
if(!$newReport->handleSubmission()) {
	header('Location:'.Path::toPostReport($newReport->reportInfo['locId'], $newReport->submitError));
	exit();
}

//else, redirect back to user homepage, report feed will check session for new report and ajax post
header('Location:'.Path::toUserHome());
exit();

?>