<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/SingleReport.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/FilterForm.php';


class ReportFeed {

 	//this fcn needs to be seperate from renderReports so the ajax can only call renderReports and the button isnt re-rendered 
	public static function renderFeed($reports) {
		?>
		<div id="report-feed">
			<?
			self::renderReports($reports);
			?>
 			<p class="button-container see-more"><button id="more-reports">See More Reports</button></p>
			<?
	 		self::renderFeedJS();
	 		?>
	 	</div>
	 	<?

 	} 	

 	public static function renderReports($reports) {
 		?>
		<ul class="reports">
			<?
			if (!$reports) {
				?>
				<div class="no-data">No Public Reports</div>
				<?
				return;
			}
			//render Feed loop
			$expanded = true;
			foreach ($reports as $report) {
				SingleReport::renderSingleReport($report, array('expanded'=>$expanded));
				$expanded = false;
			}
			?>
		</ul>
		<?
	}

	public static function renderFeedJS() {
		?>
		<script type="text/javascript">
			//Load report details
			$('.report').off('click').on('click', function(event){
				// if ($(event.target).parents('.buoy-data').length) {
				// 	event.stopPropagation();
				// }
				$(this).toggleClass('expanded');
			});
			updateNumReports();
			loadThumbnails();
			(function(){
				var limit  = 6;
					offset = 6;
			    $('#more-reports').click(function() {
			    	if (!$('#more-reports').hasClass('disabled')) {
			    		loadMoreReports(
			    		{
			    			start    : offset,
			    			limit    : limit,
			    			queryStr : window.location.search,
			    			feed     : 'home'
			    		}, function(){
			    			offset = offset + limit;	
			    		});
			    	}
			    });				
			})();
		</script> 			
		<?
	}
}
?>