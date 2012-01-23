<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/Header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Mobile_Detect.php';

class GeneralPage {

	public $userIsLoggedIn = FALSE;
	public $userHasNewReport = FALSE;
	public $userHasLocations = FALSE;
	public $userLocations = array();
	public $userName = NULL;
	public $userEmail = NULL;
	public $userId = NULL;		

	public function loadData() {

		$this->user = new User;

		if ($this->user->userIsLoggedIn()) {
			$this->user->getCurrentUser();
			$this->userIsLoggedIn = TRUE;
			$this->userId = $this->user->userId;
			$this->userName = $this->user->userName;
			$this->userEmail = $this->user->userEmail;
			$this->userLocations = Persistence::getUserLocations($this->userId);
			if (isset($this->user->newReport)) {
				$this->userHasNewReport = TRUE;

				if ($this->user->newReport['reporterHasLocation'] == '0') {
					array_unshift($this->userLocations, array('id'=>$this->user->newReport['locId'], 'locname'=>$this->user->newReport['locName']));
				}
			}
			if (!empty($this->userLocations)) {
				$this->userHasLocations = TRUE;		
			} 
			$this->userInfo = array('id'=>$this->userId, 'name'=>$this->userName, 'locations'=>$this->userLocations, 'reportStatus'=>$this->user->reportStatus);
		}
		$this->detect = new Mobile_Detect();
		$this->isMobile = $this->detect->isMobile();

		$this->siteTitle = 'Buoy Report';
		$this->pageTitle = $this->siteTitle;
				
	}

	public function renderPage() {
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<? $this->renderHead(); ?>
			<body class="br <?= $this->getBodyClassName() ?>">
				<div id="wrapper">
					<?		
					$this->renderHeader(); 
					?>
					<div id="outer-container" class="container">
						<?
						$this->renderBodyContent();
						?>	
					</div>
					<?		
					$this->renderFooter(); 
					?>	
				</div>	
				<? $this->renderFooterJs() ?>
			</body>
		</html>
		<?	
	}

	public function renderHead() {
		?>
		<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
		<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
		<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
		<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
		<head>

			<!-- Basic Page Needs
		  ================================================== -->
			<meta charset="utf-8">
			<title><?=$this->pageTitle?></title>
			<meta name="description" content="Buoy Report allows surfers, fisherman, or anyone who interacts with the ocean to save buoy measurements from a time in the past for their own records.">
			<meta name="author" content="hodara">
			<!--[if lt IE 9]>
				<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->

			<!-- Mobile Specific Metas
		  ================================================== -->
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
			<?
			$this->renderCss();
			$this->renderJs();
			?>	
		</head>		
		<?
	}

	public function renderCss() {
		global $local_dev;

		if ($local_dev) {
			?><link rel="stylesheet" type="text/css" href="<?=Paths::toCss()?>styles.php" media="screen, projection" /><?
		} else {
			?><link rel="stylesheet" type="text/css" href="<?=Paths::toCss()?>cache.css" media="screen, projection" /><?
		}		
		?>

		<!-- Favicons
		================================================== -->
		<link rel="shortcut icon" href="<?=Paths::toImages()?>favicon.ico">
		<link rel="apple-touch-icon" sizes="57x57" href="<?=Paths::toImages()?>apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?=Paths::toImages()?>apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?=Paths::toImages()?>apple-touch-icon-114x114.png">		
		<?
	}

	public function renderJs() {
		global $local_dev;

		if ($local_dev) {
			?><script type="text/javascript" src="<?=Paths::toJs()?>jquery-1.7.1.min.js"></script><?
		} else {
			?><script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script><?
		}

		?>
		<script type="text/javascript" src="<?=Paths::toJs()?>jquery.validate.min.js"></script>	
		<? 		
	}

	public function renderHeader() {

		$options['userIsLoggedIn'] = $this->userIsLoggedIn;
		$options['userHasLocations'] = $this->userHasLocations;
		$options['userLocations'] = $this->userLocations;
		$options['userName'] = $this->userName;
		$options['userEmail'] = $this->userEmail;
		$options['userId'] = $this->userId;

		$header = new Header($options);
		$header->renderHeader();
	}

	public function getBodyClassName() {}

	public function renderBodyContent() {
		?>
		<div class="three columns">
			<?
			$this->renderLeft();
			?>
		</div>
		<div id="main-container" class="nine columns">
			<?
			$this->renderMain();		
			?>			
		</div>
		<div class="four columns">
			<?
			$this->renderRight();
			?>
		</div>
		<?		
	}

	public function renderFooter() {
		?>
		<div class="footer"> 
			<div class="top"></div>
			<div class="bottom">
				<div class="text">
					<p class="footer-nav">
					<p class="copyright">&copy; <a href="http://www.hodaradesign.com" target="_blank">hodara design 2012</a>&nbsp;|&nbsp;<a href="<?=Paths::toAbout()?>">About Buoy Report</a></p>
					<p class="noaa">All buoy and tide data from <a target="_blank" href="http://noaa.gov">NOAA</a></p>
				</div> 
			</div>
		</div>
		<?
	}

	public function renderFooterJs() {
		?>
		<script>
			(function($){ 
				$('input[placeholder], textarea[placeholder]').placeHoldize();

				$('.toggle-btn').click(function(){
					$(this).next('.toggle-area').toggle();
				})
			})(jQuery);					
		</script>
		<?
	}		
}
?>