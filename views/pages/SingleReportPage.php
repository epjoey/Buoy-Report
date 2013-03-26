<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class SingleReportPage extends Page {

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = 'Single Report';

		$this->report = ReportService::getReport($id, array(
			'includeBuoyData' => true,
			'includeTideData' => true,
			'includeLocation' => true,
			'includeSublocation' => true,
			'includeBuoyModel' => true,
			'includeTideStationModel' => true,
			'includeReporter' => true
		));
		if(!$this->report) {
			header("HTTP/1.0 404 Not Found");			
			include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
			exit();	
		}	
	}

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
	}
}