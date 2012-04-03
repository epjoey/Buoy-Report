<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/modules/SearchModule.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/GeneralPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ItemList.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';



class BuoyPage extends GeneralPage {

	public $reporters = NULL;
	public $isLocationReporters = FALSE;
	public $locationId = NULL;
	public $locInfo = NULL;

	public function loadData() {
		parent::loadData();
		$this->pageTitle = 'Buoys';
		$this->buoys = Persistence::getAllStations('buoy',200);
		$this->searchModule = new SearchModule;
	}

	public function getBodyClassName() {
		return 'buoy-list-page list-page';
	}	


	public function renderJs() {
		parent::renderJs();
		$this->searchModule->renderFilterJs();	
	}

	public function renderBodyContent() {
		?>
		<h1 class="list-title">Buoys</h1>

		<div class="search-container">
			<? $this->searchModule->renderFilterInput(); ?>
		</div>

		<div class="buoy-page-list grid-list-container" id="grid-list-container">
			<?
			// $options['items'] = $this->buoys;
			// $count = count($options['items']);
			// /* pass in js trigger */
			// for($i = 0; $i < $count; $i++) {
			// 	$options['items'][$i]['path'] = "javascript:";
			// 	$options['items'][$i]['name'] = "<span class=\"buoy-id\">" . 
			// 		$options['items'][$i]['buoyid'] . 
			// 		"</span><span class=\"buoy-desc\">" . 
			// 		$options['items'][$i]['name'] . 
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
						<li class="block-list-item" buoyid="<?=html($buoy['buoyid'])?>" onclick="handleBuoyClick($(this))";>
							<a class="item-inner" href="javascript:">
								<span class="name">
									<span class="buoy-id"><?= html($buoy['buoyid']) ?></span>&nbsp;
									<span class="buoy-desc"><?= html($buoy['name']) ?></span> 
								</span>
							</a>
							<div class='iframe-container' style="display:none"></div>
							<div class="edit-delete">
								<a class="edit" href="<?=Path::toEditBuoy($buoy['buoyid'], null)?>">Edit</a>
								<?/*<span class="delete"></span>*/?>
							</div>
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

			function handleBuoyClick(elem) {
				if (elem.hasClass('loaded')) {
					elem.find('.iframe-container').toggle();
				} else {
					showBuoyDetails(elem);	
				}
			}		
			
		 	function showBuoyDetails(elem) {
		 		var id = elem.attr('buoyid');

		 		//elem.addClass('loading');

		        $.ajax({
		            //this is the php file that processes the data
		            url: "<?=Path::toAjax()?>buoy-iframe.php?id=" + id,
		             
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