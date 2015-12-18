<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class EditReportPage extends Page {	

	public function getBodyClassName() {
		return 'report-form-page edit-report-page';
	}	

	public function renderBodyContent() {
		EditReportForm::renderEditReportForm($this->report, array(
			'statusMsg' => $this->reportFormStatus,
			'needPicup' => $this->needPicup
		));
	}
}