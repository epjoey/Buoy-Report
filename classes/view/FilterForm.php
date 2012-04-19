<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/ReportOptions.php';

class FilterForm {

	public static function renderFilterModule($options = array()) {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				self::renderFilterForm($options);
				?>
			</div>
		</div>		
		<?		
	}

	public static function renderFilterForm($options = array()) {		
		?>	
		<form method="get" action="" id="filter-form">
			<span class="cancel filter-trigger" onclick="$(this).parents('#outer-container').toggleClass('filter-expanded');">[ close ]</span>
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

			if(isset($options['sublocations'])) {
				?>
				<div class="field location">
					<select name="sublocation" id="sublocation">
						<option value="0">Any Sub-Spot</option>
						<? 
						foreach ($options['sublocations'] as $sublocation) { 
							?>
							<option value="<?= $sublocation->sl_id ?>" <?= 
								isset($_GET['sublocation']) && $_GET['sublocation'] == $sublocation->sl_id ? "selected='selected'" :'';
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
			<script type="text/javascript">
			    //Filter ajax
			    // $('#filter-submit').click(function() {  
			    // 	filterReports(onPage);
			    // 	return false;
			    // });			
			</script>
		</form>		
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