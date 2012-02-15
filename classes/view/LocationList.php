<?php

class LocationList {

	protected $hasLocations = FALSE;
	protected $locations = array(); //m.d. array containing name, id, buoys, and tide
	protected $selectedLocation = NULL;
	protected $toPost = FALSE;
	protected $showSeeMore = FALSE;
	protected $showSeeAll = FALSE;
	protected $showAddLocation = FALSE;
	protected $isSearchable = FALSE; //only one per page!

	public function __construct($options = array()) {
		if (isset($options['locations']) && !empty($options['locations'])) {
			$this->hasLocations = TRUE;
			$this->locations = $options['locations'];
		}
		if (isset($_GET['location']) && $_GET['location']) {
			$this->selectedLocation = $_GET['location'];
		}
		if (isset($options['toPost']) && $options['toPost'] != FALSE) {
			$this->toPost = TRUE;
		}
		if (isset($options['showSeeMore']) && $options['showSeeMore'] != FALSE) {
			$this->showSeeMore = TRUE;
		}
		if (isset($options['showSeeAll']) && $options['showSeeAll'] != FALSE) {
			$this->showSeeAll = TRUE;
		}		
		if (isset($options['showAddLocation']) && $options['showAddLocation'] != FALSE) {
			$this->showAddLocation = TRUE;
		}	
		if (isset($options['isSearchable']) && $options['isSearchable'] != FALSE) {
			$this->isSearchable = TRUE;
		}		
	}
	
	public function renderLocations() {
		if ($this->hasLocations) { 
			?>
			<ul class="locations items">
				<?
				foreach ($this->locations as $location):
					?>
					<li class="location block-list-item <?=isset($this->selectedLocation) && $this->selectedLocation == $location['id'] ? "selected" : "" ?>">
						<a class="location-inner" href="<?= $this->toPost ? Path::toPostReport($location['id']) : Path::toLocation($location['id'])?>">
							<span class="name"><?= html($location['locname']) ?></span>
						</a>
						<span class="notification-icons">
							<? if (isset($location['buoy1'])) { ?>
								<span class="buoy-icon icon" title="<?=html($location['locname'])?> has buoy stations"></span>
							<? } ?>
							<? if (isset($location['tidestation'])) { ?>
								<span class="tide-icon icon" title="<?=html($location['locname'])?> has a tide station"></span>
							<? } ?>	
						</span>							
					</li>
				<? 
				endforeach; 
				?>
			</ul>
		<?
			
		} else {
			?>
			<div class="no-data"><span>No Locations</span></div>
			<?
		}

		if ($this->isSearchable) {
			/* to show during search */
			?>
			<div id="no-data-container" style="display:none">
				<p class="no-data">No Locations match your criteria</p>
			</div>			
			<?
		}
		
		if ($this->showSeeMore) {
			?>
			<a class="block-link outer-link see-more" href="<??>"><span>More Location</span></a>
			<?
		}

		if ($this->showAddLocation) {
			?>
			<a class="block-link outer-link add" href="<?=Path::toSubmitLocation();?>"><span>+ Add Location</span></a>
			<?
		}

		if ($this->showSeeAll) {
			?>
			<a class="block-link outer-link see-all" href="<?=Path::toLocations(null, $this->toPost);?>"><span>See all locations</span></a>
			<?
		}				
	}

}
?>