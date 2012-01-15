<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';



function processReportForm() {
	$imageIsReady = FALSE;
	$submitError = NULL;	


	if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {

		if (!is_uploaded_file($_FILES['upload']['tmp_name'])) {
			$this->submitError = 'Error uploading file';
			return FALSE;
		}
		if (preg_match('/^image\/p?jpeg$/i', $_FILES['upload']['type'])) {
			$imageExt = '.jpg';
		} else if (preg_match('/^image\/gif$/i', $_FILES['upload']['type'])) {
			$imageExt = '.gif';
		} else if (preg_match('/^image\/(x-1)?png$/i', $_FILES['upload']['type'])) {
			$imageExt = '.png';
		} else {
			$this->submitError = 'You must upload a .gif, .jpeg, or .png';
			return FALSE;	
		}	
		$this->imageIsReady = TRUE;	
	}

	//success. load homepage and do this with ajax
	$report = new Report;
	$report->loadData($this->userId, $this->location);
	if ($this->imageIsReady) {
		$report->uploadImage($imageExt);
	}
	$report->submitData();

	return TRUE;
}

?>