<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/Header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Mobile_Detect.php';

class GeneralPage {
	

	public function loadData() {

		$this->user = new User;

		if ($this->user->isLoggedIn)
			$this->user->getUserLocations($this->user->id);

		$this->detect = new Mobile_Detect();
		$this->isMobile = $this->detect->isMobile();

		$this->siteTitle = 'Buoy Report';
		$this->pageTitle = $this->siteTitle;
		$this->header = new Header;
				
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
			$this->renderGoogleAnalytics();
			?>	
					
		</head>		
		<?
	}


	public function renderCss() {
		global $local_dev;

		if ($local_dev) {
			?><link rel="stylesheet" type="text/css" href="<?=Path::toCss()?>css-aggregator.php" media="screen, projection" /><?
		} else {
			?><link rel="stylesheet" type="text/css" href="<?=Path::toCss()?>cache.css" media="screen, projection" /><?
		}		
		?>

		<!-- Favicons
		================================================== -->
		<link rel="shortcut icon" href="<?=Path::toImages()?>favicon.ico">
		<link rel="apple-touch-icon" sizes="57x57" href="<?=Path::toImages()?>apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?=Path::toImages()?>apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?=Path::toImages()?>apple-touch-icon-114x114.png">		
		<?
	}

	public function renderJs() {
		global $local_dev;

		if ($local_dev) {
			?><script type="text/javascript" src="<?=Path::toJs()?>jquery-1.7.1.min.js"></script><?
		} else {
			?><script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script><?
		}

		?>
		<script type="text/javascript" src="<?=Path::toJs()?>jquery.validate.min.js"></script>	
		<script type="text/javascript" src="<?=Path::toJs()?>overlay.js"></script>	
		<? 		
	}

	public function renderGoogleAnalytics() {
		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-28578724-1']);
		  _gaq.push(['_setDomainName', 'buoy-report.com']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>	
		<?

	}

	public function renderHeader() {

		$this->header->renderHeader($this->user);
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
					<p class="copyright">&copy; <a href="http://www.hodaradesign.com" target="_blank">hodara design 2012</a>&nbsp;|&nbsp;<a href="<?=Path::toAbout()?>">About Buoy Report</a>&nbsp;|&nbsp;<a class="" href="<?=Path::toBuoys()?>">Browse Buoys</a>&nbsp;|&nbsp;<a class="" href="<?=Path::toReporters()?>">Browse Reporters</a></p>
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