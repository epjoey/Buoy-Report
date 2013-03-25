<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReportFeed {

 	//this fcn needs to be seperate from renderReports so the ajax can only call renderReports and the button isnt re-rendered 
	public static function renderFeed($reports, $paginationParams) {
		?>
		<div id="report-feed">
			<?
			self::renderReports($reports);
			?>
 			<p class="button-container see-more"><button id="more-reports">See More Reports</button></p>			
	 	</div>
		<script type="text/javascript">
			
			updateNumReports();
			loadThumbnails();

			//Load report details
			$('#report-feed').delegate('.report','click', function(event){
				// if ($(event.target).parents('.buoy-data').length) {
				// 	event.stopPropagation();
				// }
				$(this).toggleClass('expanded');
			});
			
			(function(paginationParams){
				var limit  = paginationParams.limit;
					offset = paginationParams.limit;
			    
			    $('#more-reports').click(function() {
			    	if (!$('#more-reports').hasClass('disabled')) {
			    		loadMoreReports(
			    		{
			    			start    : offset,
			    			limit    : limit,
			    			queryStr : window.location.search,
			    			feed     : paginationParams.feedLocation
			    		}, function(){
			    			offset = offset + limit;	
			    		});
			    	}
			    });				
			})(<?= json_encode($paginationParams) ?>);
		</script> 		 	
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
			//$expanded = true;
			foreach ($reports as $report) {
				SingleReport::renderSingleReport($report, array('expanded'=>$expanded));
				$expanded = false;
			}
			?>
		</ul>
		<?
	}
}
?>