<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';

class SinglePostPage extends GeneralPage {

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = $this->siteTitle . '';

		$this->report = Persistence::getReportById($id);
		if(!isset($this->report)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Path::to404());
			exit();	
		}		
		$this->locationInfo = Persistence::getLocationInfoById($this->report['locationid']);
		$this->showDetails = TRUE;
		
		$this->reportView = new SingleReport;
		$this->reportView->loadData($this->report, $this->locationInfo, $this->showDetails);
	}

	public function getBodyClassName() {
		return 'single-post-page';
	}	

	public function renderBodyContent() {
		$this->reportView->renderSingleReport();
		if ($this->report['reporterid'] == $this->user->id) {
			?>	
				<p class="button-container edit-report">
					<a class="button" href="<?=Path::toEditPost($this->report['id'])?>">Edit Report</a>
				</p>
			<?
		}
	}
	

}