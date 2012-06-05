<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';


class ReportFeed {

	static $limit = 6; 
	static $offset = 0;

 	//this fcn needs to be seperate from renderReports so the ajax can only call renderReports and the button isnt re-rendered 
	public static function renderFeed($reports) {

		self::renderReports($reports);

 		//doesnt work yet with refactoring
 		if (count($reports) >= self::$limit)
 			self::renderSeeMoreButton();

 	} 	

 	public static function renderReports($reports) {
		if (empty($reports) && !isset($_SESSION['new-report'])) { //no new report
			?>
				<div class="no-data">No Public Reports</div>
			<?
		} else {
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

				//render Feed loop
				if (!empty($reports)) {
					foreach ($reports as $report) {
						SingleReport::renderSingleReport($report);
					}
				}
				?>
			</ul>
			<script type="text/javascript">
				//Load report details
				$('.report').off('click').on('click', function(){
					loadReportDetails($(this));
				});
				updateNumReports();
				loadThumbnails();
			</script> 			
			<?
		}
	} 		


	static function renderSeeMoreButton() {
		?>
			<p class="button-container see-more">
				<button id="more-reports">See More Reports</button>
				<script type="text/javascript">
				    //More reports ajax

				    $('#more-reports').click(function() {
				    	if (!$('#more-reports').hasClass('disabled')) {
				    		loadMoreReports('#filter-form');
				    	}
				    });				
				</script>
			</p>
		<?
	}
}
?>