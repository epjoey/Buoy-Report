<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';



class SearchModule {

	//only needed for autocompleter
	public function loadItems( $items ) {
		$this->items = $items;
	}

	public function renderFilterInput($label = NULL) {	
		?>
		<input type="text" class="text-input" name="q" id="query" placeholder="Search <?=isset($label) ? $label : '';?>" value="" />
		<?		
	}

	public function renderFilterJs() {	
	?>
		<script type="text/javascript">
			jQuery(function ($) {

				$(document).ready(function(){
					$('#query').focus();
				});	


				//add class to all items
				$('#grid-list-container li').addClass('visible');
				$("#query").keyup(function (event) {

					if (event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 13) {
						//if esc is pressed or nothing is entered clear the value of search box
				    	if ($(this).val() == '') {  
				      		$('#grid-list-container li').removeClass('visible').show().addClass('visible'); 	
				      	}	
					    var filter = $(this).val(), 
					    	count = 0;
					    $("#grid-list-container li").each(function () {
					        if ($(this).text().search(new RegExp(filter, "i")) < 0) {
					            $(this).hide();
					            $(this).removeClass('visible');
					        } else {
					            $(this).show();
					            $(this).addClass('visible');
					            count++;
					        }
					    });
					    $("#filter-count").text(count);

						if (count == 1) {
							$("#grid-list-container li.visible").addClass('selected');
						} else {
							$("#grid-list-container li").removeClass('selected');
						}
					}
				
     
			        /*key navigation through elements*/
			        if (event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 13) {
			            
			            var results = $('#grid-list-container li.visible');
			             
			            var current = results.filter('.selected'),
			                next;

			            switch ( event.keyCode ) {
			                case 38: // Up
			                    next = current.prev('.visible');
			                    
			                    break;
			                case 40: // Down
			                    if (!results.hasClass('selected')) {
			                           results.first().addClass('selected');
			                    }
			                    next = current.next('.visible');                
			                    break; 
			                case 13: // Enter
			                	//if on the buoy list page, hitting enter triggers the onclick event (opens the div with the iframe)
			                	if (searchModuleList == 'buoylist') {
			                		handleBuoyClick(current);
			                		return false;
			                	}
			                    if (results.hasClass('selected')) {
			                        location.href = current.find('a').attr('href');
			                        return false;
			                    }
			                    break;
			            }
			            
			            //only check next element if up and down key pressed
			            if ( next.is('li') ) {
			                current.removeClass('selected');
			                next.addClass('selected');
			            }
			        
			            //update text in searchbar
			            if (results.hasClass('selected')) {                
			                $('#query').val($('.selected .name').text());                
			            }
			            
			            //set cursor position
			            if(event.keyCode === 38) return false;
			            
			            return;
			        }

			        if ($("#grid-list-container li.visible").length < 1) {
			        	$('#no-data-container').show();
			        } else {
			        	$('#no-data-container').hide();
			        }


				});   


				$('#query').bind('keydown keypress',function(event)
				{
				    if (event.keyCode == 38 || event.keyCode == 40) 
				    {
				        event.preventDefault();
				    }
				});

			});	
		</script>
	<?
	}		

}