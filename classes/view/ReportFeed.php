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

	public function loadData($options=array()) {
		$this->options = $options;

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
			if (!strtotime($_GET['date'])) $this->filterError = 'Date is not valid. Must be in mm/dd/yyyy format.';
			else $this->filters['date'] = strtotime($_GET['date']) + 60*60*24;
		}	
		
		//report feed needs either reporters or locations
		if (!empty($this->filters['locations']) || !empty($this->filters['reporters']))
			$this->reports = Persistence::getReports($this->filters,$this->limit);	
			
	}

	public function renderReportFeed() {

		$this->renderFilterNote();
 
		if (!isset($this->reports) && !isset($_SESSION['new-report'])) {
			?>
				<div class="no-data">No Reports</div>
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
				<p class="filter-error error"><?=$this->filterError?></p>
				<?	
			}
			$exp = 'Showing last ' . $this->limit . ' reports';
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
		$onPage = $this->options['on-page'];
		?>
		<script type="text/javascript">
			$(document).ready(function() {
     
			    //Filter AJAX
			    $('#filter-submit').click(function() {        
			         
			        var feed = $('#report-feed-container');
			        var params = {
				        'quality' : $('select[name=quality]').val(),
				        'image' : $('select[name=image]').val(),
				        'text' : $('input[name=text]').val(),
				        'date' : $('input[name=date]').val(),
				        'on-page' : "<?=$onPage?>"		        	
			       	
			        };

			        //Get the data from all the fields
			        if ($('input[name=location]').length>0) {
			        	 params['location'] = $('input[name=location]').val();
			        }
			        if ($('select[name=location]').length>0) {
			        	 params['location'] = $('select[name=location]').val();
			        }		        
			        if ($('input[name=reporter]').length>0) {
			        	 params['reporter'] = $('input[name=reporter]').val();
			        }		        

			         
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
			            success: function (reports) {    
			            	$('#outer-container').removeClass('filter-expanded');                        
			                feed.html(reports); 
			                feed.removeClass('loading');
							$('#report-feed-container .report').on('click', function(){
								loadReportDetails($(this));
							});
		                
	   		            }       
			        });
			         
			        //cancel the submit button default behaviours
			        return false;
			    }); 
		
				//Filter trigger toggle
				$('.filter-trigger').click(function(){
					$('#outer-container').toggleClass('filter-expanded');
				});

				$('.reports .report').on('click', function(){
					loadReportDetails($(this));
				});

			});

			function loadReportDetails(report) {
			
			//$('.reports .report').each(function(){
				
			//	$(this).click(function(){

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