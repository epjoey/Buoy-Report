<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class SingleReportPage extends Page {

	public function getBodyClassName() {
		return 'single-report-page';
	}	

	public function renderBodyContent() {
		SingleReport::renderSingleReport($this->report, array('showDetails'=>true));
		
		if ($this->report->reporterid == $this->user->id) {
			?>	
			<p class="button-container edit-report">
				<a class="button" href="<?=Path::toEditReport($this->report->id)?>">Edit Report</a>
			</p>
			<?
		}
		?>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					BR.images.lazyLoad('.image-container img');
				});
			})(jQuery);
		</script> 
		<?	
	}
}