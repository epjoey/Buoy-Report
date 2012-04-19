<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/FilterForm.php';

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
								

		/*reporter info array passed in from page or ajax */
		if (!empty($options['reporters'])) {

			foreach($options['reporters'] as $reporter) {

				//array of ID's for SQL query
				$this->filters['reporters'][] = $reporter['id'];

				//indexing by reporter id so each report can access reporter info w/o quering DB.
				$this->reporters[$reporter['id']] = $reporter;
			}
		}				

		/*location info array passed in from page or ajax */
		if (!empty($options['locations'])) {

			foreach($options['locations'] as $location) {

				//array of ID's for SQL query
				$this->filters['locations'][] = $location['id'];

				//indexing by location id so each report can access location info w/o quering DB.
				$this->locations[$location['id']] = $location;
			}
		}

		/* sublocation info array passed in from page or ajax */
		if (!empty($options['sublocation'])) {
			$this->filters['sublocation'] = $options['sublocation'];
			$this->sublocationInfo = $options['sublocationInfo'];
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

 		?>

 		<?	

 	}

 	public function renderReportList() {

 		FilterForm::renderOpenFilterTrigger();

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
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							loadNewReport();
						});	
					</script>
					<?						
				}

				//render Feed loop
				if (isset($this->reports)) {
					foreach ($this->reports as $report) {
						SingleReport::renderSingleReport($report);
					}
				}
				?>
			</ul>
			<script type="text/javascript">
				//Load report details
				$('.reports .report').on('click', function(){
					loadReportDetails($(this));
				});
			</script>				
	
			<?
		}
	}

	protected function renderSeeMoreButton() {
		?>
			<p class="button-container see-more">
				<button id="more-reports">See More Reports</button>
				<script type="text/javascript">
				    //More reports ajax

				    $('#more-reports').click(function() {
				    	var onPage = $(this).parents('#report-feed-container').attr('onPage');
				    	if (!$('#more-reports').hasClass('disabled')) {
				    		loadMoreReports(onPage);
				    	}
				    });				
				</script>
			</p>
		<?
	}

	//move this to its own class
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
			if (isset($this->sublocationInfo)) {
				$exp .= "<span>(" . html($this->sublocationInfo->sl_name) . ")</span>";
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
				var onPage = $('#report-feed-container').attr('onPage');				
				
			    $('#filter-submit').click(function() {  
			    	filterReports(onPage);
			    	return false;
			    });
			    	

				//load the thumnails
				loadThumbnails();	
				updateNumReports()			    

			});
		</script>
		<?
	}
}
?>