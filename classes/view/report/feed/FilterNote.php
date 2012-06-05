<?
class FilterNote {
	public static function renderFilterNote($filters = array()) {

		/* updated via js rendered below feed */
		$exp = "Showing last <b id='numReports'>--</b> reports";

		?>
		<div class="filter-explain">
			<?		
			if (isset($filters['error'])) {
				?>
				<p class="filter-error error"><?=html($filters['error'])?></p>
				<?			
			}

			if (isset($filters['quality'])) {
				$qualities = ReportOptions::quality();
				$text = $qualities[$filters['quality']];
				$exp .= '<span>with ' . $text . ' sessions</span>';
			}
			if (isset($filters['text'])) {
				$exp .= "<span>with text '" . html($filters['text']) . "'</span>";
			}
			if (isset($filters['image'])) {
				$array = ReportOptions::hasImage();
				$text = $array[$filters['image']];
				$exp .= "<span>" . $text . "</span>";
			}
			if (isset($filters['date'])) { //how to get format in here?
				$exp .= "<span>on or before " . strftime("%D",$this->filters['date']) . "</span>";
			}
			if (isset($filters['location'])) { //how to get name in here?
				$exp .= "<span>from " . html($filters['location']) . "</span>";
			}
			
			print $exp;						
			?>
		</div>
		<?
	}
}


?>