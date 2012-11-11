<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';

class SinglePostPage extends Page {

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = $this->siteTitle . '';

		$this->report = Persistence::getReportById($id);
		if(!isset($this->report)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Path::to404());
			exit();	
		}	
	}

	public function getBodyClassName() {
		return 'single-post-page';
	}	

	public function renderBodyContent() {
		SingleReport::renderSingleReport($this->report, array('showDetails'=>true));
		if ($this->report['reporterid'] == $this->user->id) {
			?>	
				<p class="button-container edit-report">
					<a class="button" href="<?=Path::toEditPost($this->report['id'])?>">Edit Report</a>
				</p>
			<?
		}
	}
	

}