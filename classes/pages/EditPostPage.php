<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/EditReportForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';




class EditPostPage extends GeneralPage {

	public $submitError = NULL;

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = $this->siteTitle . '';

		$this->report = Persistence::getReportById($id);

		if(($this->report['reporterid'] != $this->user->id) || !isset($this->report)) {
			header('Location:'.Path::to404());
			exit();	
		}		
		
		if(isset($_GET['error']) && $_GET['error']) {
			$this->submitError = $_GET['error'];
		}
		
		//todo::make this left join
		$this->report['locationInfo'] = Persistence::getLocationInfoById($this->report['locationid']);

		$this->report['locationInfo']['sublocations'] = Persistence::getSubLocationsByLocation($this->report['locationid']);
		
		//for picup callback. - mobile app redirection based on session var
		setPicupSessionId('edit-report-form', $id);
	}		

	public function getBodyClassName() {
		return 'report-form-page edit-report';
	}	

	public function renderBodyContent() {
		EditReportForm::renderEditReportForm($this->report, $this->submitError, $this->isMobile);
	}	

	public function renderFooterJs() {

		parent::renderFooterJs();

		if($this->detect->isIphone() || $this->detect->isIpad()) {
			?>
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js"></script>
			<script type="text/javascript" src="<?=Path::toJs()?>picup.js"></script>
			<script type="text/javascript">
				document.observe('dom:loaded', function(){
					usePicup('<?=Path::toMobileImageProcess()?>', 'report_form');
				});
			</script>	
			<?	
		}	
	}	

	

}