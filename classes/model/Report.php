<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/BouyData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/TideData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';

class Report {
	
	public $reportInfo = array();
	public $submitError = NULL;
	public $reportId = NULL;


	public function handleSubmission() {

		$this->reportInfo['reporterId'] = intval($_POST['reporterid']);
		$this->reportInfo['locId'] = intval($_POST['locationid']);
		$this->reportInfo['locName'] = $_POST['locationname'];
		$this->reportInfo['reportDate'] = intval(gmdate("U")); 
		/* calculates date of observation if in the past */	
		$offset = abs(intval($_POST['time_offset'])) * 60 * 60;
  		$this->reportInfo['observationDate'] = intval(gmdate("U", time()-$offset));
  		$this->reportInfo['reporterHasLocation'] = $_POST['reporterhaslocation'];
			
		if (isset($_POST['bouy1'])) {
			$this->reportInfo['bouy1'] = $_POST['bouy1'];
		}
		if (isset($_POST['bouy2'])) {
			$this->reportInfo['bouy2']  = $_POST['bouy2'];
		}
		if (isset($_POST['bouy3'])) {
			$this->reportInfo['bouy3'] = $_POST['bouy3'];
		}				
		if (isset($_POST['tidestation'])) {
			$this->reportInfo['tidestation']  = $_POST['tidestation'];
		}
		if (!empty($_POST['text'])) {
			$this->reportInfo['text'] = $_POST['text'];
		}
		if (!empty($_POST['quality'])) {
			$this->reportInfo['quality'] = $_POST['quality'];
		} else {
			$this->submitError = 'no-quality';
			return FALSE;
		}	
		
		//image copied into directory during construction. wierd, I know.
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

			$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . date('Y') . '/' . date('m') . '/' . $this->reportInfo['reporterId'] .'.'. date('d.G.i.s') . $imageExt;
			
			//stored in DB. full path prepended
			$this->reportInfo['imagepath'] = date('Y') . '/' . date('m') . '/' . $this->reportInfo['reporterId'] . '.' . date('d.G.i.s') . $imageExt;

			$image = new SimpleImage();
			$image->load($_FILES['upload']['tmp_name']);
			$image->fitDimensions(1000,1000);

			chmod($_FILES['upload']["tmp_name"], 0777);
			chmod($imagePath, 0777);
			move_uploaded_file($_FILES['upload']["tmp_name"], $imagePath); 
			
			
			$this->submitError = 'file-save';	

						
		} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
			$this->reportInfo['imagepath'] = rawurldecode($_POST['remoteImageURL']);
		}
			
		/* Storing report in session */			
		if (!isset($_SESSION)) session_start();
		$_SESSION['new-report'] = $this->reportInfo; 			
		
	}

	public function processReport() {

		if (isset($this->submitError)) {
			return FALSE;				
		} else {
			return TRUE;
		}		
	}
	
	public static function submitData($reportInfo){
		
		$reportId = Persistence::insertReport($reportInfo);
		
		/* for each bouy included, check if it still exists, scrape it, and enter it into db */
		for ($i=1; $i<=3; $i++) {
			if (isset($reportInfo['bouy' . $i])) {
				$bouy = $reportInfo['bouy' . $i];
				$data = new BouyData($bouy, $reportInfo['observationDate']);
				if ($data->bouyExists && $data->bouyHasAccurateData()) {
					//NTH:check if noaa data is updated, if not just reuse last report
					Persistence::insertBouyData(
						$reportId,
						array(
							'bouy' => $bouy, 
							'gmttime' => $data->bouyDate, 
							'winddir' => $data->getWindDir(), 
							'windspeed' => $data->getWindSpeed(), 
							'swellheight' => $data->getWaveHeight(), 
							'swellperiod' => $data->getDomWavePeriod(), 
							'swelldir' => $data->getMeanWaveDir(), 
							'tide' => $data->getTide()
						)
					);
				}				
			}
		}

		if (isset($reportInfo['tidestation'])) {
			$tide = new TideData($reportInfo['tidestation'], $reportInfo['observationDate']);
			if ($tide->stationExists && $tide->stationHasAccurateData()) {
				Persistence::insertTideData($reportId, $tide->tide, $tide->residual, $tide->tideDate);
			}
		}
			
		return $reportId;

	}

}