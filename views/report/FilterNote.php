<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class FilterNote {
	public static function renderFilterNote($filters = array()) {

		/* updated via js rendered below feed */
		$exp = "Showing last <b id='numReports'>--</b> reports";

		?>
		<div class="filter-explain">
			<?		
			if ($filters['error']) {
				?>
				<p class="filter-error error"><?=html($filters['error'])?></p>
				<?			
			}

			if ($filters['quality']) {
				$qualities = ReportOptions::quality();
				$text = $qualities[$filters['quality']];
				$exp .= '<span>with ' . $text . ' sessions</span>';
			}
			if ($filters['text']) {
				$exp .= "<span>with text '" . html($filters['text']) . "'</span>";
			}
			if ($filters['image']) {
				$array = ReportOptions::hasImage();
				$text = $array[$filters['image']];
				$exp .= "<span>" . $text . "</span>";
			}
			if ($filters['obsdate']) { //how to get format in here?
				$d = new DateTime($filters['obsdate']);
				$exp .= "<span>on or before " . $d->format('M d, Y') . "</span>";
			}
			if ($filters['location']) { //how to get name in here?
				$exp .= "<span>from " . html($filters['location']) . "</span>";
			}
			
			print $exp;						
			?>
		</div>
		<?
	}
}


?>