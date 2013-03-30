<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


class ReporterPage extends Page {

	public function getBodyClassName() {
		return 'reporter-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		SearchModule::renderFilterJs();
	}

	public function renderBodyContent() {
		?>
		<h1 class="list-title">
			<? 
			if($this->isLocationReporters) {
				?>
				<a href="<?=Path::toLocation($this->location->id)?>"><?=$this->location->locname?></a> 
				<?
			} else {
				print 'Buoy';
			} 
			?> 
			Reporters
		</h1>

		<div class="search-container">
			<? SearchModule::renderFilterInput(); ?>
		</div>

		<div class="rep-page-list">
			<?
			$options['items'] = $this->reporters;
			$count = count($options['items']);
			for($i = 0; $i < $count; $i++) {
				$options['items'][$i]['path'] = Path::toProfile($options['items'][$i]['id']);
			} 
			$options['itemLabel'] = 'reporter';
			$options['pathToAll'] = Path::toReporters();
			$options['isSearchable'] = TRUE;
			ItemList::renderList($options);
			?>
		</div>
		<?
	}

}