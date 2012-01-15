<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/GeneralPage.php';

class MobileImageProcessPage extends GeneralPage {

	public $location = NULL;

	public function loadData($location) {
		$this->location = $location;
		$this->pageTitle = 'Upload Successful';
	}

	public function renderHeader() {}
	
	public function renderFooter() {}
	
	public function renderFooterJs() {}

	public function getBodyClassName() {
		return 'process-mobile-image';
	}	

	public function renderBodyContent() {
		?>
		<h1>Image Upload Complete</h1>
		<a class="block-link" href="javascript:window.open('<?=Paths::toPostReport($this->location)?>'+window.location.hash, 'report_form');window.close()">Return to Report Form</a>	
		<?	
	}

}

