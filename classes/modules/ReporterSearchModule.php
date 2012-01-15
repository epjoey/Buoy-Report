<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/view/LocationList.php';



class LocationSearchModule {

	//only needed for autocompleter
	public function loadLocations( $locations ) {
		$this->locations = $locations;
	}

	public function renderFilterInput() {	
		?>
		<input type="text" class="text-input" name="q" id="query" placeholder="Search Locations" value="" />
		<?		
	}

	public function renderFilterJs() {	
	?>
		<script type="text/javascript">
			jQuery(function ($) {
				//add class to all items
				$('.loc-page-list .location').addClass('visible');
				$("#query").keyup(function (event) {

					if (event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 13) {
						//if esc is pressed or nothing is entered clear the value of search box
				    	if (event.keyCode == 27 || $(this).val() == '') {  
				      		$(this).val('');  
				      		//also remove any lingering visible class names and add classname to all
				      		$('.loc-page-list .location').removeClass('visible').show().addClass('visible'); 	
				      	}	
					    var filter = $(this).val(), 
					    	count = 0;
					    $(".loc-page-list .location").each(function () {
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
							$(".loc-page-list .location.visible").addClass('selected');
						} else {
							$(".loc-page-list .location").removeClass('selected');
						}
					}
				
     
			        /*key navigation through elements*/
			        if (event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 13) {
			            
			            var results = $('.loc-page-list .location.visible');
			             
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

			        if ($(".loc-page-list .location.visible").length < 1) {
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

	public function renderAutoCompleteJs() {	
		?>
		<script type="text/javascript">
			
			var options, a;
			$(function(){
			  	options = { 
				  	serviceUrl:'<?= Paths::toAjax(); ?>locationAutoComplete.php', 
				    minChars:2, 
				    delimiter: /(,|;)\s*/, // regex or character
				    maxHeight:400,
				    width:280,
				    zIndex: 1,
				    deferRequestBy: 0, //miliseconds
				    //params: { country:'Yes' }, //aditional parameters
				    noCache: false, //default is false, set to true to disable caching
				    // callback function:
				    onSelect: function(value, data){ alert('You selected: ' + value + ', ' + data); },
				    // local autosuggest options:
				    lookup: [<? 
				    ob_start();
				    foreach ($this->locations as $loc) {
				    	echo $loc['locname'].',';
				    }
				    $output = ob_get_clean();
				    echo rtrim($output, ',');
				    ?>]
				};
			 	
			 	a = $('#query').autocomplete(options);
			});
		</script>
		<?
	
	}	

}