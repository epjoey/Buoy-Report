<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';

class FilterForm {

	public static function renderFilterModule($filterOptions = array(), $autoFilters = array()) {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				self::renderFilterForm($filterOptions, $autoFilters);
				?>
			</div>
		</div>		
		<?		
	}

	public static function renderFilterForm($filterOptions = array(), $autoFilters = array()) {	
		?>	
		<form method="get" action="" id="filter-form">
			<span class="cancel filter-trigger" onclick="$(this).parents('#outer-container').toggleClass('filter-expanded');">[ close ]</span>
			<?					
			if (isset($autoFilters['reporterId'])) {
				?>
				<input type="hidden" name="reporter" value="<?=$autoFilters['reporterId']?>" />
				<?	
			}				
			if (!empty($autoFilters['reporterIds'])) {
				?>
				<input type="hidden" name="reporters" value="<?=implode(',', $autoFilters['reporterIds'])?>" />
				<?	
			}
			if (isset($autoFilters['locationId'])) {
				?>	
				<input type="hidden" name="location" value="<?= $autoFilters['locationId']?>"/>
				<?
			}				
			if (!empty($autoFilters['locationIds'])) {
				?>	
				<input type="hidden" name="locations" value="<?= implode(',', $autoFilters['locationIds'])?>"/>
				<?
			}					

			if(!empty($filterOptions['locationObjects'])) {
				?>
				<div class="field location">
					<label for="date">By Location:</label>
					<select name="location" id="location">
						<option value="0">-- Choose --</option>
						<? 
						foreach ($filterOptions['locationObjects'] as $location) { 
							?>
							<option value="<?= $location['id'] ?>" <?= 
								returnRequest('location') == $location['id'] ? "selected='selected'" :'';
								?>><?= $location['locname'] ?></option>
							<? 
						} 
						?>
					</select>
				</div>
				<?	
			}

			if(!empty($filterOptions['sublocationObjects'])) {
				?>
				<div class="field location">
					<label for="date">By SubLocation:</label>
					<select name="sublocation" id="sublocation">
						<option value="0">-- Choose --</option>
						<? 
						foreach ($filterOptions['sublocationObjects'] as $sublocation) { 
							?>
							<option value="<?= $sublocation->sl_id ?>" <?= 
								returnRequest('sublocation') == $sublocation->sl_id ? "selected='selected'" :'';
								?>><?= $sublocation->sl_name ?></option>
							<? 
						} 
						?>
					</select>
				</div>
				<?	
			}			
			?>

			<div class="field quality">			
				<label for="date">By Session Quality:</label>
				<select name="quality" id="quality">
					<option value="0">-- Choose --</option>
					<? foreach (ReportOptions::quality() as $key=>$value): ?>
						<option value="<?= $key ?>" <?=
							returnRequest('quality') == $key ? "selected='selected'" :'';
						?>><?= $value ?></option>
					<? endforeach; ?>
				</select>
			</div>

			<div class="field image">
				<label for="date">With/Without Image:</label>
				<select name="image" id="image">
					<option value="0">-- Choose --</option>
					<? foreach (ReportOptions::hasImage() as $key=>$value): ?>
						<option value="<?= $key ?>" <?= 
							returnRequest('image') == $key ? "selected='selected'" :'';
						?>><?= $value ?></option>
					<? endforeach; ?>
				</select>
			</div>

			<div class="field text-search">
				<label for="date">Containing Text:</label>			
				<input type="text" name="text" id="text-search" class="text-input"  placeholder="Enter text..." />
			</div>

			<div class="field date">
				<label for="date">On/Before Date:</label>
				<input type="text" name="date" id="date" class="text-input"  placeholder="mm/dd/yyyy" />
			</div>
			
			<input type="submit" id="filter-submit" value="Filter" />
			<p class="reset">
				<? 
					$url = $_SERVER['PHP_SELF'];
					if (returnRequest('reporter')) {
						$url .= '?reporter=' . returnRequest('reporter');
					} else if (returnRequest('location')) {
						$url .= '?location=' . returnRequest('location');
					}

				?>
				<a href="<?=$url?>">Reset</a>
			</p>
		</form>	
		<script type="text/javascript">
		    // $('#filter-submit').click(function() {  
		    // 	filterReports($(this).parents('#filter-form'));
		    // 	return false;
		    // });			
		</script>
		<?
	}

	public static function renderOpenFilterTrigger(){
		?>
		<span class="filter-trigger mobile-only" id="filter-trigger" onclick="$(this).parents('#outer-container').toggleClass('filter-expanded');">
			<span class='filter-label'>FILTER</span>
			<img src="<?=Path::toImages()?>/filter-icon.png" width="20" height="27" id="filter-icon" title="Filter Reports"/>
		</span>	
		<?
	}	
}

?>