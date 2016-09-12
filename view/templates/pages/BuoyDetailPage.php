<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class BuoyDetailPage extends Page {


	public function getBodyClassName() {
		$str = "buoy-detail-page ";
		return $str;
	}	
	
	public function renderBodyContent() {
		?>
		<div class="three columns">
			<?
			$this->renderLeft();
			?>
		</div>
		<div class="four columns pull-right">
			<?
			$this->renderRight();
			?>
		</div>
		<div id="main-container" class="nine columns pull-left">
			<?
			$this->renderMain();
			?>
		</div>
		<?
	}
	
	public function renderLeft() {
		$filterOptions = array();
		FilterForm::renderFilterModule($filterOptions, array());
	}
	
	public function renderMain() {
		$this->renderBuoyDetails();		
		$this->renderBuoyReports();	

	}	


	public function renderBuoyDetails() {
		?>
		<div class="loc-details">
			<h1><a href="<?=Path::toBuoy($this->buoy->buoyid)?>"><?= html($this->buoy->buoyid)?></a></h1>
			<h3><a href="<?=Path::toBuoy($this->buoy->buoyid)?>"><?= html($this->buoy->name)?></a></h3>
		</div>
		<?
	}


	private function renderBuoyReports() {
		?>
		<div class="reports-container">
			<? FilterForm::renderOpenFilterTrigger(); ?>
			<div id="report-feed-container">
				<? 
				FilterNote::renderFilterNote(array_merge($this->reportFilters, array()));
				ReportFeed::renderFeed($this->reports, array(
					'limit' => $this->numReportsPerPage,
					'reportFilters' => $this->reportFilters
				));
				?>
			</div>
		</div>		
		<?
	} 

	public function renderRight() {

		$this->renderCurrentData();
		?>
		<p class="sb-section">
			<a href="<?= Path::toEditBuoyPage($this->buoy->buoyid) ?>">Edit buoy</a>
		</p>
		<?
	}

	private function renderCurrentData() {
		?>
		<div class="current-data">	
			<div class="buoy-current-data sb-section">	
				<iframe src="http://www.ndbc.noaa.gov/widgets/station_page.php?station=<?=$this->buoy->buoyid?>" style="width:100%; min-height: 300px"></iframe>
			</div>
		</div>
		<?
	}	
	


}