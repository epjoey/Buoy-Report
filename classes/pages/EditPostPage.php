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

		$this->reportInfo = Persistence::getReportById($id);

		if(($this->reportInfo['reporterid'] != $this->user->id) || !isset($this->reportInfo)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Path::to404());
			exit();	
		}		
		
		$this->locationInfo = Persistence::getLocationInfoById($this->reportInfo['locationid']);
		$this->showDetails = TRUE;
		
		$this->editForm = new EditReportForm;
		$this->editForm->loadData($this->reportInfo, $this->locationInfo, $this->showDetails);

		//for picup callback. - mobile app redirection based on session var
		setPicupSessionId('edit-report-form', $id);
	}		

	public function getBodyClassName() {
		return 'report-form-page edit-report';
	}	

	public function renderBodyContent() {
		$this->editForm->renderEditReportForm($this->submitError, $this->isMobile);
	}

	public function afterSubmit() {
		if ($_POST['submit'] == "update-report") {

			if(!$this->processReport()) {
				$this->renderPage();
				exit();
			}

			Persistence::updateReport($this->reportInfo);
			header('Location:'.Path::toSinglePost($this->reportInfo['id']));
		}

		if ($_POST['submit'] == 'delete-report') {
			Persistence::deleteReport($this->reportInfo['id']);
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Path::toUserHome());
			exit();	
		}		
	}	

	private function processReport(){	

		if (isset($_POST['delete-image']) && $_POST['delete-image'] == 'true') {
			$this->reportInfo['imagepath'] = '';
		}

		//image copied into directory during form handle. wierd, I know.
		if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
			
			$uploadStatus = Report::handleUpload($_FILES['upload'], $this->user->id);

			if (isset($uploadStatus['error'])) {
				$this->submitError = $uploadStatus['error']; 
				return FALSE;	
			} else if (isset($uploadStatus['imagepath'])) {
				$this->reportInfo['imagepath'] = $uploadStatus['imagepath'];
			}
						
		} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
			$this->reportInfo['imagepath'] = rawurldecode($_POST['remoteImageURL']);
		}		

		$this->reportInfo['text'] = $_POST['text'];


		if (!empty($_POST['quality'])) {
			$this->reportInfo['quality'] = $_POST['quality'];
		} else {
			$this->submitError = 'no-quality';
			return FALSE;
		}					
		
		return TRUE;	
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