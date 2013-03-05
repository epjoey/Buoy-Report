<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';


class ReportFormPage extends Page {

	public $submitError = NULL;
	public $needPicup = FALSE;

	public function loadData() {
		parent::loadData();
		$this->locationId = $_GET['location'];
		$this->location = LocationService::getLocation($this->locationId, array(
			'includeSublocations' => true,
			'includeBuoys' => true,
			'includeTideStations' => true
		));
		if (!isset($this->location)) {
			header('Location:'.Path::to404());
			exit();	
		}	
		$this->pageTitle = $this->location->locname . ' Report';
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
		return 'report-form-page report-' . $this->location->id;
	}		

	public function renderBodyContent() {
		ReportForm::renderReportForm($this->location, $this->user, $this->submitError, $this->needPicup);	
	}

	public function renderFooterJs() {

		parent::renderFooterJs();

		if($this->needPicup) {
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