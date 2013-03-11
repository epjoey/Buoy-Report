<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/report/ReportComment.php';

class ReportCommentPersistence {
	static function getCommentsForReport($reportId) {
		$rawComments = array(poop,poo,poooop);
		$comments = array();
		foreach ($rawComments as $comment) {
			$comments[] = new ReportComment($comment);
		}
		return $comments;
	}
}
?>