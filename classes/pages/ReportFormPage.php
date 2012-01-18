<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';


class ReportFormPage extends GeneralPage {

	public $submitError = NULL;
	public $needPicup = FALSE;

	public function loadData() {
		parent::loadData();
		$this->location = $_GET['location'];
		$this->locInfo = Persistence::getLocationInfoById($this->location);
		if (!isset($this->locInfo)) {
			header('Location:'.Paths::to404());
			exit();	
		}		
		$this->pageTitle = $this->locInfo['locname'] . ' Report';
		if (isset($_GET['error']) && $_GET['error']) {
			$this->submitError = $_GET['error'];
		}

		if ($this->detect->isAppleDevice()) {
			$this->needPicup = TRUE;
		}
		
		//for picup callback
		if (!isset($_SESSION)) session_start();
		$_SESSION['location-for-image'] = $this->location;
	
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
		$form->renderReportForm($this->locInfo, $this->userInfo, $this->submitError, $this->needPicup);	
	}

	public function renderFooterJs() {

		parent::renderFooterJs();


		if($this->detect->isIphone() || $this->detect->isIpad()) {
			?>
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js"></script>
			<script type="text/javascript" src="<?=Paths::toJs()?>picup.js"></script>
			<script type="text/javascript">
				document.observe('dom:loaded', function(){
					usePicup('<?=Paths::toMobileImageProcess()?>', 'report_form');
				});
			</script>	
			<?	
		}	
	}
	
}