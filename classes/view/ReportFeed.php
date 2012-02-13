<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';

class ReportFeed {

	private $reports = NULL;
	private $locations = array();
	private $reporters = array();
	private $limit = 6;
	private $filterError = NULL;
	private $filters = array();
	private $options = array();
	protected $offset = 0;

	public function loadData($options=array()) {
		$this->options = $options;

		if (!empty($options['offset']))
			$this->offset = $options['offset'];

		if (!empty($options['limit']))
			$this->limit = $options['limit'];
								

		/*reporter info array passed in from page */
		if (!empty($options['reporters'])) {

			foreach($options['reporters'] as $reporter) {

				//array of ID's for SQL query
				$this->filters['reporters'][] = $reporter['id'];

				//indexing by reporter id so each report can access reporter info w/o quering DB.
				$this->reporters[$reporter['id']] = $reporter;
			}
		}				

		/*location info array passed in from page */
		if (!empty($options['locations'])) {

			foreach($options['locations'] as $location) {

				//array of ID's for SQL query
				$this->filters['locations'][] = $location['id'];

				//indexing by location id so each report can access location info w/o quering DB.
				$this->locations[$location['id']] = $location;
			}
		}

			
		//get param overrides passed in reporters	
		if (!empty($_GET['reporter'])) {
			$this->filters['reporters'] = array($_GET['reporter']);
		}		

		//get param overrides passed in locations
		if (!empty($_GET['location'])) {
			$this->filters['locations'] = array($_GET['location']);
		}	

		if (!empty($_GET['text'])) {
			$this->filters['text'] = $_GET['text'];
		}

		if (!empty($_GET['quality'])) {
			$this->filters['quality'] = $_GET['quality'];
		}

		if (!empty($_GET['image'])) {
			$this->filters['image'] = $_GET['image'];
		}

		if (!empty($_GET['date'])) {
			if (strlen($_GET['date']) < 10 || !strtotime($_GET['date'])) 
				$this->filterError = 'Date is not valid. Must be in mm/dd/yyyy format.';
			else 
				$this->filters['date'] = strtotime($_GET['date']) + 60*60*24;
		}
				
		//report feed needs either reporters or locations
		if (!empty($this->filters['locations']) || !empty($this->filters['reporters']))
			$this->reports = Persistence::getReports($this->filters,$this->limit, $this->offset);	
			
	}

	public function renderReportFeed() {

		$this->renderFilterNote();

		$this->renderReportList();
 
 		if (isset($this->reports) && count($this->reports) >= $this->limit)
 			$this->renderSeeMoreButton();

 	}

 	public function renderReportList() {

		if (!isset($this->reports) && !isset($_SESSION['new-report'])) {
			?>
				<div class="no-data">No Public Reports</div>
			<?
		} else {
			?>
				<ul class="reports">
					<?

					//render empty element to fill with new report that will be loaded via ajax
					if (isset($_SESSION['new-report']) && $_SESSION['new-report']) {
						$this->renderNewReport($_SESSION['new-report']);
					}

					//render Feed loop
					if (isset($this->reports)) {
						foreach ($this->reports as $report) {
							$singleReport = new SingleReport;

							if (!empty($this->locations)) {
								$locInfo = $this->locations[$report['locationid']];
							} else {
								$locInfo = NULL;
							}
							$singleReport->loadData($report, $locInfo);
							$singleReport->renderSingleReport();
						}
					}
					?>
				</ul>
			<?
		}
	}

	protected function renderSeeMoreButton() {
		?>
			<p class="button-container see-more">
				<button id="more-reports">See More Reports</button>
			</p>
		<?
	}

	private function renderNewReport($newReport) {
		?>
		<span id="new-report" class="loading"></span>
		<script type="text/javascript">
				
		 	$('#new-report').load('<?=Paths::toAjax()?>new-report.php',
				function(){
					$('#new-report').removeClass('loading');
					$('#new-report .report').click(function(){
						$(this).toggleClass('expanded');
						$(this).toggleClass('collapsed');
					})	
		 		}
		 	);		
		</script>
		<?
	}

	private function renderFilterNote() {	
		?>
		<div class="filter-explain">
			<?
			if (isset($this->filterError)) {
				?>
				<p class="filter-error error"><?=html($this->filterError)?></p>
				<?	
			}
			$numReports = $this->limit + $this->offset;
			$exp = "Showing last <b id='numReports'>" . $numReports . "</b> reports";

			if (isset($this->filters['quality'])) {
				$qualities = ReportOptions::quality();
				$text = $qualities[$this->filters['quality']];
				$exp .= '<span>with ' . $text . ' sessions</span>';
			}
			if (isset($this->filters['text'])) {
				$exp .= "<span>with text '" . html($this->filters['text']) . "'</span>";
			}
			if (isset($this->filters['image'])) {
				$array = ReportOptions::hasImage();
				$text = $array[$this->filters['image']];
				$exp .= "<span>" . $text . "</span>";
			}
			if (isset($this->filters['date'])) {
				$exp .= "<span>on or before " . html($_GET['date']) . "</span>";
			}
			if (isset($_GET['location'])) {
				if (isset($this->locations[$_GET['location']]['locname'])) {
					$locName = $this->locations[$_GET['location']]['locname'];
					$exp .= "<span>from " . html($locName) . "</span>";
				}
			}
			if (isset($this->options['on-page']) && $this->options['on-page'] == 'homepage') {
				$exp .= "<span>from my locations</span>";
			}
			
			print $exp;						
			?>
		</div>					
		<?		
	}

	public function renderReportFeedJS() {
		?>
		<script type="text/javascript">
			$(document).ready(function() {	

			    var feed = $('#report-feed-container');
				var onPage = feed.attr('onPage');				
				
				bindEventHandlers(feed, onPage);

				//load the thumnails
				loadThumbnails();	
				updateNumReports(feed)			    

			});

			function bindEventHandlers(feed, onPage) {

			    //Filter ajax
			    $('#filter-submit').click(function() {  
			    	filterReports(feed, onPage);
			    	return false;
			    });

			    //More reports ajax
			    $('#more-reports').click(function() {
			    	if (!$('#more-reports').hasClass('disabled')) {
			    		loadMoreReports(feed, onPage);
			    		event.stop(); //click being registered exponentionally for some reason	
			    	}
			    });

				//Filter trigger toggle
				$('.filter-trigger').click(function(){
					$('#outer-container').toggleClass('filter-expanded');
				});

				//Load report details
				$('.reports .report').on('click', function(){
					loadReportDetails($(this));
				});					
			}

		    function getFilterValues() {
				
				var filterValues = {
					
					//Set the current filter form values
			        'quality' : $('select[name=quality]').val(),
			        'image' : $('select[name=image]').val(),
			        'text' : $('input[name=text]').val(),
			        'date' : $('input[name=date]').val()
				}

				//On some pages, a locationid is pre-selected using a hidden input
		        if ($('input[name=location]').length>0) {
		        	filterValues['location'] = $('input[name=location]').val();
		        }

		        //On other pages, the user can choose a location
		        if ($('select[name=location]').length>0) {
		        	filterValues['location'] = $('select[name=location]').val();
		        }
		        	
		        //On some pages, a reporterid is pre-selected	        
		        if ($('input[name=reporter]').length>0) {
		        	filterValues['reporter'] = $('input[name=reporter]').val();
		        }
		        				
				return filterValues;		    	
		    }

		    function filterReports(feed, onPage) {    
		    
		    	params = getFilterValues();   
		    	params['on-page'] = onPage;

		        var data = '';
		        for(var index in params) {
				  data += index + "=" + params[index] + "&";
				} 

		        //show the loading sign
		        feed.addClass('loading');
		         
		        //start the ajax
		        $.ajax({
		            //this is the php file that processes the data
		            url: "<?=Paths::toAjax()?>filter-reports.php", 
		             
		            //GET method is used
		            type: "GET",
		 
		            //pass the data         
		            data: data,     
		             
		            //Do not cache the page
		            cache: false,
		             
		            //success
		            success: function(reports) {   
		            	$('#outer-container').removeClass('filter-expanded');  
		                feed.html(reports); 
		                feed.removeClass('loading');
		                loadThumbnails();		               
						bindEventHandlers(feed, onPage);
   		            }       
		        });
		    }; 	
		    
		    function loadMoreReports(feed, onPage) {
		    	params = getFilterValues(); 
		    	params['on-page'] = onPage;
		    	params['num-reports'] = feed.find('.report').length;

		    	var data = '';
		        for(var index in params) {
				  	data += index + "=" + params[index] + "&";
				} 
				console.log(data);

				//find the last list of reports (only one until "See more reports" is clicked)
		        reportsList = feed.find('ul.reports').last();

		        //insert temporary loading sign after current list
		        reportsList.after("<div id='temp-loading' class='loading'></div>");

		        //start the ajax
		        $.ajax({
		            //this is the php file that processes the data
		            url: "<?=Paths::toAjax()?>load-more-reports.php", 
		             
		            //GET method is used
		            type: "GET",
		 
		            //pass the data         
		            data: data,     
		             
		            //Do not cache the page
		            cache: false,
		             
		            //success
		            success: function(reports) { 
		            	$('#temp-loading').remove();  
		                reportsList.after(reports); 
		                loadThumbnails();		               
						bindEventHandlers(feed, onPage);
						
						//rewrite feed count at top
						updateNumReports(feed);

						//disable button if no more reports
						//console.log(reports.match('<li'));
						if (reports.match('<li') == null)
							disableMoreReportsButton();
   		            }       
		        });				 
		    }		

			function loadReportDetails(report) {

				var detailSection = report.find('.detail-section'),
					reportId = report.attr('reportid'),
					obuoys = report.attr('hasbuoys'),
					otideStation = report.attr('hastide'),
					otimezone = report.attr('tz'),
					oreportTime = report.attr('reporttime'),
					oreporterId = report.attr('reporterid'),
					oimagePath = report.attr('imagepath');
					
				if (report.hasClass('collapsed')) {
					report.removeClass('collapsed').addClass('expanded');	
					
					if (detailSection.hasClass('loaded')) {
						return;
					}

					detailSection.addClass('loading');	
				 	detailSection.load('<?=Paths::toAjax()?>report-details.php?id=' + reportId,
				 		{
				 			buoys:obuoys,
				 			tideStation:otideStation,
				 			timezone:otimezone,
				 			reportTime:oreportTime,
				 			reporterId:oreporterId,
				 			imagePath:oimagePath
				 		}
				 		, function(){
							detailSection.removeClass('loading');
							detailSection.addClass('loaded');
				 		}
				 	)
				} else {
					report.removeClass('expanded').addClass('collapsed');
				}
			}

			function loadThumbnails(){
				$('.image-container.thumbnail-image img').each(function(elem){
					src = $(this).attr('realUrl');
					$(this).attr('src', src);
					$(this).parent('.thumbnail-image').removeClass('loading').addClass('loaded');
				});				
			}

			function updateNumReports(feed){
				elem = feed.find('#numReports').first();
				numReports = feed.find('.report').length;
				elem.text(numReports);
			}

			function disableMoreReportsButton(){
				$('#more-reports').addClass('disabled');

			}

		</script>
		<?
	}

	public function renderFilterIcon() {
		?>
		<span class="filter-trigger mobile-only" id="filter-trigger">
			<span class='filter-label'>FILTER</span>
			<img src="<?=Paths::toImages()?>/filter-icon.png" width="20" height="27" id="filter-icon" title="Filter Reports"/>
		</span>			
		<?
	}
}
?>