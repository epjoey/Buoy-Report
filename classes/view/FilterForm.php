<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';

class FilterForm {

	public function renderFilterForm($options = array()) {
		
		?>
		<form method="get" action="" id="filter-form">
			<span class="cancel filter-trigger">[ close ]</span>
			<?

			//if on profile page, reports automatically filtered by reporter (need this here because query string gets overwritten when filtering)
			if (isset($_GET['reporter']) && $_GET['reporter']) {
				?>
				<input type="hidden" name="reporter" value="<?=$_GET['reporter']?>" />
				<?	
			}
			// Locations not passed in on location page
			if (isset($options['location-page'])) {
				?>	
				<input type="hidden" name="location" value="<?=$options['location-page']?>"/>
				<?
			}			

			if(isset($options['locations']) && $options['showlocations']) {
				?>
				<div class="field location">
					<select name="location" id="location">
						<option value="0">Any Location</option>
						<? 
						foreach ($options['locations'] as $location) { 
							?>
							<option value="<?= $location['id'] ?>" <?= 
								isset($_GET['location']) && $_GET['location'] == $location['id'] ? "selected='selected'" :'';
								?>><?= $location['locname'] ?></option>
							<? 
						} 
						?>
					</select>
				</div>
				<?	
			}
			?>
			<div class="field quality">			
				<select name="quality" id="quality">
					<option value="0">Any Quality</option>
					<? foreach (ReportOptions::quality() as $key=>$value): ?>
						<option value="<?= $key ?>" <?=
							isset($_GET['quality']) && $_GET['quality'] == $key ? "selected='selected'" :'';
						?>><?= $value ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<div class="field image">
				<select name="image" id="image">
					<? foreach (ReportOptions::hasImage() as $key=>$value): ?>
						<option value="<?= $key ?>" <?= 
							isset($_GET['image']) && $_GET['image'] == $key ? "selected='selected'" :'';
						?>><?= $value ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<div class="field text-search">
				<input type="text" name="text" id="text-search" class="text-input"  placeholder="Containing text..." />
			</div>
			<div class="field date">
				<label for="date">On/Before Date:</label>
				<input type="text" name="date" id="date" class="text-input"  placeholder="mm/dd/yyyy" />
			</div>
				
			<input type="submit" id="filter-submit" value="Filter" />
			<p class="reset">
				<? 
					$url = $_SERVER['PHP_SELF'];
					if (isset($_GET['reporter'])) {
						$url .= '?reporter=' . $_GET['reporter'];
					} else if (isset($_GET['location'])) {
						$url .= '?location=' . $_GET['location'];
					}

				?>
				<a href="<?=$url?>">Reset</a>
			</p>
		</form>
		<?
	}
}

?>