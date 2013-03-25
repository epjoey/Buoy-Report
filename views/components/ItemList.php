<?php

class ItemList {

	
	public static function renderList($options = array()) {
		$label = isset($options['itemLabel']) ? $options['itemLabel'] : 'Item';

		?>
		<div class="grid-list-container" id="grid-list-container">
		<?		
			if (!empty($options['items'])) { 
				?>
				<ul class="items">
					<?
					foreach ($options['items'] as $item):
						self::renderListItem($item);
					endforeach; 
					?>
				</ul>
				<?
			} else {
				?>
				<div class="no-data"><span>No <?= $label ?>s</span></div>
				<?
			}

			if (isset($options['isSearchable']) && $options['isSearchable']) {
				/* to show during search */
				?>
				<div id="no-data-container" style="display:none">
					<p class="no-data">No <?= $label ?>s match your criteria</p>
				</div>			
				<?
			}			
			
			if (isset($options['pathToMore']) && $options['pathToMore']) {
				?>
				<a class="block-link outer-link more" href="<?=$options['pathToMore']?>"><span>More <?= $label ?>s</span></a>
				<?
			}

			if (isset($options['pathToAdd']) && $options['pathToAdd']) {
				?>
				<a class="block-link outer-link add" href="<?=$options['pathToAdd']?>"><span>+ Add <?= $label ?></span></a>
				<?
			}

			if (isset($options['pathToAll']) && $options['pathToAll']) {
				?>
				<a class="block-link outer-link all" href="<?=$options['pathToAll']?>"><span>All <?= $label ?>s</span></a>
				<?
			}		
		?>
		</div>
		<?						
	}

	public static function renderListItem($item) {
		?>
		<li class="block-list-item">
			<a class="item-inner" href="<?= html($item['path']) ?>">
				<span class="name"><?= html($item['name']) ?></span>
			</a>						
		</li>
		<? 		
	}
}
?>