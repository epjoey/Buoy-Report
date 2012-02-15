<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';


class GridList {

	protected $itemLabel = 'Item';
	protected $hasItems = FALSE;
	protected $items = array(); //m.d. array containing name, id, buoys, and tide
	protected $showSeeAllLink = FALSE;
	protected $showAddLink = FALSE;
	protected $showSeeMoreLink = FALSE;
	protected $pathToMore = NULL;
	protected $pathToAll = NULL;
	protected $pathToAdd = NULL;
	protected $basePathToItem = NULL; //Paths
	protected $isSearchable = FALSE; //only one per page!


	public function __construct($options = array()) {
		if (isset($options['itemLabel']) && $options['itemLabel'] != FALSE) {
			$this->itemLabel = $options['itemLabel'];
		}	
		if (isset($options['items']) && !empty($options['items'])) {
			$this->hasItems = TRUE;
			$this->items = $options['items'];
		}
		if (isset($options['showSeeAllLink']) && $options['showSeeAllLink'] != FALSE) {
			$this->showSeeAllLink = TRUE;
		}	
		if (isset($options['showAddLink']) && $options['showAddLink'] != FALSE) {
			$this->showAddLink =  $options['showAddLink'];
		}	
		if (isset($options['showMoreLink']) && $options['showMoreLink'] != FALSE) {
			$this->showMoreLink =  $options['showMoreLink'];
		}			
		if (isset($options['pathToMore']) && $options['pathToMore'] != FALSE) {
			$this->pathToMore =  $options['pathToMore'];
		}	
		if (isset($options['pathToAdd']) && $options['pathToAdd'] != FALSE) {
			$this->pathToAdd =  $options['pathToAdd'];
		}	
		if (isset($options['pathToAll']) && $options['pathToAll'] != FALSE) {
			$this->pathToAll =  $options['pathToAll'];
		}											
		if (isset($options['isSearchable']) && $options['isSearchable'] != FALSE) {
			$this->isSearchable = TRUE;
		}				
	}
	
	public function renderGridList() {
		?>
		<div class="grid-list-container" id="grid-list-container">
		<?		
			if ($this->hasItems) { 
				?>
				<ul class="items">
					<?
					foreach ($this->items as $item):
						?>
						<li class="block-list-item">
							<a class="item-inner" href="<?= html($item['path']) ?>">
								<span class="name"><?= html($item['name']) ?></span>
							</a>						
						</li>
					<? 
					endforeach; 
					?>
				</ul>
				<?
			} else {
				?>
				<div class="no-data"><span>No <?= $this->itemLabel ?>s</span></div>
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
			
			if ($this->showSeeMoreLink) {
				?>
				<a class="block-link outer-link more" href="<?=$this->pathToMore?>"><span>More <?= $this->itemLabel ?>s</span></a>
				<?
			}

			if ($this->showAddLink) {
				?>
				<a class="block-link outer-link add" href="<?=$this->pathToAdd;?>"><span>+ Add <?= $this->itemLabel ?></span></a>
				<?
			}

			if ($this->showSeeAllLink) {
				?>
				<a class="block-link outer-link all" href="<?=$this->pathToAll?>"><span>All <?= $this->itemLabel ?>s</span></a>
				<?
			}		
		?>
		</div>
		<?						
	}
}
?>