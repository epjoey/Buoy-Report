<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyPage extends Page {

	public function getBodyClassName() {
		return 'buoy-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		SearchModule::renderFilterJs();	
	}

	public function renderBodyContent() {
		?>
		<h1 class="list-title">Buoys</h1>

		<div class="search-container">
			<? SearchModule::renderFilterInput(); ?>
		</div>

		<div class="buoy-page-list grid-list-container" id="grid-list-container">
			<?
			// $options['items'] = $this->buoys;
			// $count = count($options['items']);
			// /* pass in js trigger */
			// for($i = 0; $i < $count; $i++) {
			// 	$options['items'][$i]['path'] = "javascript:";
			// 	$options['items'][$i]->name = "<span class=\"buoy-id\">" . 
			// 		$options['items'][$i]->buoyid . 
			// 		"</span><span class=\"buoy-desc\">" . 
			// 		$options['items'][$i]->name . 
			// 		"</span>";
			// } 
			// $options['itemLabel'] = 'buoy';
			// $options['pathToAll'] = Path::toBuoys();
			// $options['isSearchable'] = TRUE;
			// ItemList::renderList($options);



			if (!empty($this->buoys)) { 
				?>
				<ul class="items buoys">
					<?
					foreach ($this->buoys as $buoy):
						?>
						<li class="block-list-item" buoyid="<?=html($buoy->buoyid)?>">
							<a class="item-inner" href="<?= Path::toBuoy($buoy->buoyid) ?>">
								<span class="name">
									<span class="buoy-id"><?= html($buoy->buoyid) ?></span>&nbsp;
									<span class="buoy-desc"><?= html($buoy->name) ?></span> 
								</span>
							</a>
						</li>
						<?
					endforeach; 
					?>
				</ul>
				<?
			} else {
				?>
				<div class="no-data"><span>No Buoys</span></div>
				<?
			}			
			?>
			<div id="no-data-container" style="display:none">
				<p class="no-data">No Buoys match your criteria</p>
			</div>		
			<a class="block-link outer-link add" href="<?=Path::toAddBuoy()?>"><span>+ Add Buoy</span></a>
			<a class="block-link outer-link all" href="<?=Path::toBuoys()?>"><span>All Buoys</span></a>		
		</div>

		<script type="text/javascript">

			var searchModuleList = 'buoylist';

			// function handleBuoyClick(elem) {
			// 	if (elem.hasClass('loaded')) {
			// 		elem.find('.iframe-container').toggle();
			// 	} else {
			// 		showBuoyDetails(elem);	
			// 	}
			// }		
			
		 	function showBuoyDetails(elem) {
		 		var id = elem.attr('buoyid');

		 		//elem.addClass('loading');

		        $.ajax({
		            //this is the php file that processes the data
		            url: "/controllers//buoy/iframe.php?id=" + id,
		             
		            //GET method is used
		            type: "GET",

		            //Do not cache the page
		            cache: false,
		             
		            //success
		            success: function(iframe) { 
		            	elem.find('.iframe-container').append(iframe).show();     				    		               
		            	elem.addClass('loaded');
		            	//elem.removeClass('loading');
   		            }       
		        });		 		
			};			
		</script>
		<?
	}

}