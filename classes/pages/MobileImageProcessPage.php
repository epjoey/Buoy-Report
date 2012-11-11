<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';


class MobileImageProcessPage extends Page {

	public $callbackUrl = NULL;

	public function loadData() {
		$this->pageTitle = 'Upload Successful';

		$sessionInfo = getPicupSessionInfo();

		if ($sessionInfo['form'] == 'report-form') {
			$this->callbackUrl = Path::toPostReport($sessionInfo['id']);
		} 
		if ($sessionInfo['form'] == 'edit-report-form') {
			$this->callbackUrl = Path::toEditPost($sessionInfo['id']);
		}
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
		<a class="block-link" href="javascript:window.open('<?=$this->callbackUrl?>'+window.location.hash, 'report_form');window.close()">Return to Report Form</a>	
		<?	
	}

}

