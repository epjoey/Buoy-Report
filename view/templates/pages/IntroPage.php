<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class IntroPage extends Page {

	public function getBodyClassName() {
		return 'intro-page list-page';
	}

	public function renderHeader() {
		Header::renderHeader($this->user, NULL, "Buoy Report!");
	}

	public function renderJs() {
		parent::renderJs();
		SearchModule::renderFilterJs();
		?>
		<script type="text/javascript">	
			$(document).ready(function(){
				$('#query').focus();
			});

			$(document).ready(function(){
				$("#login-form").validate();
			});

		</script>
		<?
	}

	public function renderBodyContent() {
		?>
			<img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="101" height="101"/>
			
			<h1 class="welcome-to-br">Buoy Report<span class="pattern"></span></h1>
<!-- 			<div class="sub-text">
				<div class="desc">
					<h2 class="tag-line" id="desc-trigger">Log buoy data after you surf.</h2>
					<a href="<?=Path::toAbout();?>" class="what">What?</a>
					<h3 class="detail" id="desc-hidden">Buoy Report lets you log weather measurements that affect your surf sessions</h3>
				</div>
				<div class="reg-browse">
					<div class="reg-container">
						<a href="<?=Path::toRegister()?>" class="reg-btn" id="reg-trigger">Get Started ></a>
					</div>
				</div>
				<div class="browse-link">
 					<a class="browse-spots" href="<?=Path::toLocations()?>">Browse Spots ></a>
 					<? /*<a class="browse-spots" href="<?=Path::toBuoys()?>">Browse Buoys ></a> */?>			
 				</div>
 				<div class="clear"></div>
			</div> -->
			<div class="search-container">
				<? SearchModule::renderFilterInput('Locations'); ?>
			</div>
			<div class="grid-list-container" id="grid-list-container">
				<?
					$options['locations'] = $this->locations;
					$options['toPost'] = FALSE;
					$options['showAddLocation'] = TRUE;
					$options['showSeeAll'] = FALSE;
					$options['isSearchable'] = TRUE;
					$list = new LocationList($options);
					$list->renderLocations();
				?>			
			</div>
			<br />
			<br />

		<?
	}

	public function renderFooterJs() {
		parent::renderFooterJs();
		?>
		<script type="text/javascript">
			
			<? if ($this->detect->isMobile()) { ?>
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
					$('#login-username').focus()
					}, 200);
			});
			
			$(window).resize(function() { console.log($(window).width()) });

		</script>	
		<?
	}	
}
?>