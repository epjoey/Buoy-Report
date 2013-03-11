<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/persistence/ReportFeedPersistence.php';

class ReportFeedService {
	static $numReportsToShowInFeed = 6;
	static function getReportsForFeed($filters, $options) {
		$ids = ReportFeedPersistence::getReportIdsForFeed($filters, $options);
		$reports = ReportService::getReports($ids, $options);
		return $reports;
	}

}
?>