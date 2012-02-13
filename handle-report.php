<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

$newReport = new Report;

//theres an error with the submission
if(!$newReport->handleSubmission()) {
	header('Location:'.Paths::toPostReport($newReport->reportInfo['locId'], $newReport->submitError));
	exit();
}

//else, redirect back to user homepage, report feed will check session for new report and ajax post
header('Location:'.Paths::toUserHome());
exit();

?>