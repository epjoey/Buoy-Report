<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/SingleReport.php';

class SingleReportPage extends Page {

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = $this->siteTitle . '';

		$this->report = ReportService::getReport($id, array(
			'includeBuoyData' => true,
			'includeTideData' => true,
			'includeLocation' => true,
			'includeSublocation' => true,
			'includeBuoyModel' => true,
			'includeTideStationModel' => true,
			'includeReporter' => true
		));
		if(!isset($this->report)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Path::to404());
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