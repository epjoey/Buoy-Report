<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/EditReportForm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/magicquotes.php';


class EditPostPage extends GeneralPage {

	public $submitError = NULL;

	public function loadData($id) {
		parent::loadData();
		$this->pageTitle = $this->siteTitle . '';

		$this->reportInfo = Persistence::getReportById($id);
		if(($this->reportInfo['reporterid'] != $this->userId) || !isset($this->reportInfo)) {
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::to404());
			exit();	
		}		
		$this->locationInfo = Persistence::getLocationInfoById($this->reportInfo['locationid']);
		$this->showDetails = TRUE;
		
		$this->editForm = new EditReportForm;
		$this->editForm->loadData($this->reportInfo, $this->locationInfo, $this->showDetails);
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
			header('Location:'.Paths::toSinglePost($this->reportInfo['id']));
		}

		if ($_POST['submit'] == 'delete-report') {
			Persistence::deleteReport($this->reportInfo['id']);
			header('HTTP/1.1 301 Moved Permanently');			
			header('Location:'.Paths::toUserHome());
			exit();	
		}		
	}	

	private function processReport(){	

		if (isset($_POST['delete-image']) && $_POST['delete-image'] == 'true') {
			$this->reportInfo['imagepath'] = '';
		}

		if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {

			if (!is_uploaded_file($_FILES['upload']['tmp_name'])) {
				$this->submitError = 'upload-file';
				return FALSE;
			}
			if (preg_match('/^image\/p?jpeg$/i', $_FILES['upload']['type'])) {
				$imageExt = '.jpg';
			} else if (preg_match('/^image\/gif$/i', $_FILES['upload']['type'])) {
				$imageExt = '.gif';
			} else if (preg_match('/^image\/(x-1)?png$/i', $_FILES['upload']['type'])) {
				$imageExt = '.png';
			} else {
				$this->submitError = 'file-type'; //unknown file type
				return FALSE;
			}	
			if (!isset($this->submitError)) {
				$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/reporter/uploads/' . date('Y') . '/' . date('m') . '/' . $this->reportInfo['reporterid'] .'.'. date('d.G.i.s') . $imageExt;
				
				//stored in DB. full path prepended
				$this->reportInfo['imagepath'] = date('Y') . '/' . date('m') . '/' . $this->reportInfo['reporterid'] . '.' . date('d.G.i.s') . $imageExt;

				$image = new SimpleImage();
				$image->load($_FILES['upload']['tmp_name']);
				$image->fitDimensions(1000,1000);
				if (!copy($_FILES['upload']['tmp_name'], $imagePath)) {
					$this->submitError = 'file-save';
					return FALSE;
				}				
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