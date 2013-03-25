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

			(function(paginationParams){
				var limit  = paginationParams.limit;
					offset = paginationParams.limit;
			    
			    $('#more-reports').click(function() {
			    	if (!$('#more-reports').hasClass('disabled')) {

						feed = $('#report-feed-container');
    
						//find the last list of reports (only one until "See more reports" is clicked)
					    reportsList = feed.find('ul.reports').last();			    		
						//insert temporary loading sign after current list
						reportsList.after("<div id='temp-loading' class='loading'></div>");
			    		
			    		loadMoreReports({
			    			start    : offset,
			    			limit    : limit,
			    			queryStr : window.location.search,
			    			feed     : paginationParams.feedLocation
			    		}, function(reports){
			    			offset = offset + limit;	
							
			    			$('#temp-loading').remove();  
							reportsList.after(reports);

							loadThumbnails();		               
							updateNumReports();							


							//disable button if no more reports
							if (reports.match('<li') == null) {
								$('#more-reports').addClass('disabled');
				            }			    			
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
			$expanded = true;
			foreach ($reports as $report) {
				SingleReport::renderSingleReport($report, array('expanded'=>$expanded));
				//$expanded = false;
			}
			?>
		</ul>
		<?
	}
}
?>