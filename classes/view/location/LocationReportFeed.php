<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/report/feed/Report.php';

class LocationReportFeed {
	
	public static function renderReportFeed($reports){
		?>
		<ul class="reports">
			<?
			foreach ($reports as $key=>$report) {
				Report::renderReport($report);
			}
			?>
		</ul>
		<?
	}

}
?>