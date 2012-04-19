<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/report/feed/Report.php';

class LocationReportFeed {
	
	public static function renderReportFeed($reports){
		?>
		<ul class="reports">
			<?

			//render empty element to fill with new report that will be loaded via ajax
			if (isset($_SESSION['new-report']) && $_SESSION['new-report']) {
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						loadNewReport();
					});	
				</script>
				<?						
			}
			
							
			foreach ($reports as $key=>$report) {
				SingleReport::renderSingleReport($report);
			}
			?>
		</ul>
		<?
	}

}
?>