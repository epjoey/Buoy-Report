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
			

			(function($, paginationParams){

				var limit  = paginationParams.limit,
					offset = paginationParams.limit,
					feed   = $('#report-feed'),
					button = feed.find('#more-reports').first();

				BR.reportFeed.onLoad(feed);
				
				feed.delegate(".report", "click", function(event) {
					if($(event.target).is('a')) {
						return true;
					}
					$(this).toggleClass("expanded");
				});

				$(button).click(function() {
					if (button.hasClass('disabled')) {
						return;
					}

					//disable during ajax request
					button.addClass('disabled');
					
					//last list of reports
					lastReportsList = feed.find('ul.reports').last();

					//insert temporary loading sign after current list
					lastReportsList.after("<div id='temp-loading' class='loading'></div>");
					//console.log(paginationParams.reportFilters);
					
					//need _.compact here
					//jquery will convert null to 'null' in ajax request
					var reportFilters = {};
					_.each(paginationParams.reportFilters, function(value, key) {
						if (value !== null) {
							reportFilters[key] = value;	
						}
					});

					var params = $.extend({
						start    : offset,
						limit    : limit
					}, reportFilters);
					
					BR.reportFeed.paginate( params, function(reports){
						offset = offset + limit;	
					
						$('#temp-loading').remove();  
						lastReportsList.after(reports);

						BR.reportFeed.onLoad(feed);							


						//disable button if no more reports
						if (reports.match('<li')) {
							button.removeClass('disabled');
						}			    			
					});
				});				
			})(jQuery, <?= json_encode($paginationParams) ?>);
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
				SingleReport::renderSingleReport($report, array('expanded'=>false));
				//$expanded = false;
			}
			?>
		</ul>
		<?
	}
}
?>