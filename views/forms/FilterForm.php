<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class FilterForm {

	public static function renderFilterModule($filterOptions = array(), $prefilters = array()) {
		?>
		<div class="filter">
			<div class="filter-inner-container">
				<h3>Filter</h2>
				<? 
				self::renderFilterForm($filterOptions, $prefilters);
				?>
			</div>
		</div>		
		<?		
	}

	public static function renderFilterForm($filterOptions = array(), $prefilters = array()) {	
		?>	
		<form method="get" action="" id="filter-form">
			<span class="cancel filter-trigger" onclick="$(this).parents('#outer-container').toggleClass('filter-expanded');">[ close ]</span>
			<?		
			foreach ($prefilters as $key=>$val) {
				?>
				<input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
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
							<option value="<?= $location->id ?>" <?= 
								$_REQUEST['location'] == $location->id ? "selected='selected'" :'';
								?>><?= $location->locname ?></option>
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
					<label for="date">By Sublocation:</label>
					<select name="sublocationid" id="sublocation">
						<option value="0">-- Choose --</option>
						<? 
						foreach ($filterOptions['sublocationObjects'] as $sublocation) { 
							?>
							<option value="<?= $sublocation->sl_id ?>" <?= 
								$_REQUEST['sublocation'] == $sublocation->sl_id ? "selected='selected'" :'';
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
							$_REQUEST['quality'] == $key ? "selected='selected'" :'';
						?>><?= $value ?></option>
					<? endforeach; ?>
				</select>
			</div>

			<div class="field image">
				<label for="date">With/Without Image:</label>
				<select name="image" id="image">
					<option value="0">-- Choose --</option>
					<? 
					foreach (ReportOptions::hasImage() as $key=>$val) { 
						?>
						<option value="<?= $key ?>" <?= $_REQUEST['image'] == $key ? "selected='selected'" :'';?>><?= $val ?></option>
						<?
					}
					?>
				</select>
			</div>

			<div class="field text-search">
				<label for="date">Containing Text:</label>			
				<input type="text" name="text" id="text-search" class="text-input"  placeholder="Enter text..." value="<?= $_REQUEST['text'] ?>" />
			</div>

			<div class="field date">
				<label for="date">On/Before Date:</label>
				<input type="text" name="obsdate" id="date" class="text-input"  placeholder="mm/dd/yyyy" value="<?= $_REQUEST['obsdate'] ?>" />
			</div>
			
			<input type="submit" id="filter-submit" value="Filter" />
			<p class="reset">
				<? 
					$url = $_SERVER['PHP_SELF'];
					if ($_REQUEST['reporter']) {
						$url .= '?reporter=' . $_REQUEST['reporter'];
					} else if ($_REQUEST['location']) {
						$url .= '?location=' . $_REQUEST['location'];
					}

				?>
				<a href="<?=$url?>">Reset</a>
			</p>
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