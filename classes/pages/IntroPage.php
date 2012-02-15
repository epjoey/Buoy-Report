<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LoginForm.php';


class IntroPage extends GeneralPage {

	public function loadData(){
		parent::loadData();
		$this->pageTitle = 'Welcome to ' . $this->siteTitle;
		$this->login = new LoginForm;			
	}

	public function getBodyClassName() {
		return 'intro-page';
	}


	public function renderJs() {
		parent::renderJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$("#login-form").validate();
			});
		</script>
		<?
	}

	public function renderHeader() {
		?>
		<div class="header"> 
			<div class="container">
					<span class="header-right login-btn" id="login-trigger"><a class="block-link" href="javascript:">LOGIN</a></span>
					<? $this->login->renderForm(); ?>
					<div class="clear"></div>
			</div>
		</div>
		<? 
	}

	public function renderBodyContent() {
		?>
			<img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="101" height="101"/>
			
			<h1 class="welcome-to-br"><span class="welcome">Welcome to</span> Buoy Report<span class="pattern"></span></h1>
			<div class="sub-text">
				<div class="desc">
					<h2 class="tag-line" id="desc-trigger">Log buoy data after you surf.</h2>
					<a href="<?=Path::toAbout();?>" class="what">What?</a>
					<h3 class="detail" id="desc-hidden">Buoy Report lets you log weather measurements that affect your surf sessions</h3>
				</div>
				<div class="reg-browse">
					<div class="reg-container">
						<a href="<?=Path::toRegister()?>" class="reg-btn" id="reg-trigger">Get Started ></a>
					</div>
 					<span class="browse-spots">or <a href="<?=Path::toLocations()?>">Browse Spots ></a></span>
				</div>
				<div class="clear"></div>
			</div>

		<?
	}

	public function renderFooterJs() {
		parent::renderFooterJs();
		?>
		<script type="text/javascript">
			
			<? if ($this->isMobile) { ?>
				var duration = 0;
			<? } else { ?>
				var duration = 250;
			<? } ?>
			
			$('#desc-trigger').click(function(){
				$('#desc-hidden').slideToggle(duration);
			});

			$('#login-trigger').click(function(){
				$('#login-form-container').slideToggle(duration);
				setTimeout(function(){
					$('#login-email').focus()
					}, 200);
			});
			
			$(window).resize(function() { console.log($(window).width()) });

		</script>	
		<?
	}	
}
?>