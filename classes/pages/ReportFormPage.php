<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';


class ReportFormPage extends GeneralPage {

	public $submitError = NULL;
	public $needPicup = FALSE;

	public function loadData() {
		parent::loadData();
		$this->locationId = $_GET['location'];
		$this->locInfo = Persistence::getLocationInfoById($this->locationId);
		if (!isset($this->locInfo)) {
			header('Location:'.Path::to404());
			exit();	
		}		
		$this->pageTitle = $this->locInfo['locname'] . ' Report';
		if (isset($_GET['error']) && $_GET['error']) {
			$this->submitError = $_GET['error'];
		}

		if ($this->detect->isAppleDevice()) {
			$this->needPicup = TRUE;
		}
		
		//for picup callback. - mobile app redirection based on session var
		setPicupSessionId('report-form', $this->locationId);
	}

	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			jQuery(document).ready(function(){
				jQuery('.text #text').focus();
				jQuery("#report-form").validate();
			});
		</script>
		<?
	}		

	public function getBodyClassName() {
		return 'report-form-page report-' . $this->locInfo['id'];
	}		

	public function renderBodyContent() {
		$form = new ReportForm;
		$form->renderReportForm($this->locInfo, $this->user, $this->submitError, $this->needPicup);	
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