<?
class FilterNote {
	public static function renderFilterNote($filters = array(), $status = null) {

		//limit
		//

		?>
		<div class="filter-explain">
			<?		
			if (isset($status)) {
				?>
				<p class="filter-error error"><?=html($this->filterError)?></p>
				<?			
			}
			?>
		</div>
	}
}


?>