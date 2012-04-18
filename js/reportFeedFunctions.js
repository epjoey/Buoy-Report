function getFilterValues() {
	
	var filterValues = {
		
		//Set the current filter form values
        'quality' : $('select[name=quality]').val(),
        'image' : $('select[name=image]').val(),
        'text' : $('input[name=text]').val(),
        'date' : $('input[name=date]').val()
	}

	//On some pages, a locationid is pre-selected using a hidden input
    if ($('input[name=location]').length>0) {
    	filterValues['location'] = $('input[name=location]').val();
    }

    //On other pages, the user can choose a location
    if ($('select[name=location]').length>0) {
    	filterValues['location'] = $('select[name=location]').val();
    }

    //On locationdetail pages with sublocations, the user can choose a sublocation
    if ($('select[name=sublocation]').length>0) {
    	filterValues['sublocation'] = $('select[name=sublocation]').val();
    }		        
    	
    //On some pages, a reporterid is pre-selected	        
    if ($('input[name=reporter]').length>0) {
    	filterValues['reporter'] = $('input[name=reporter]').val();
    }
    				
	return filterValues;		    	
}

function filterReports(onPage) {    

    feed = $('#report-feed-container');
	params = getFilterValues();   
	params['on-page'] = onPage;

    var data = '';
    for(var index in params) {
	  data += index + "=" + params[index] + "&";
	} 

	console.log(data);

    //show the loading sign
    feed.addClass('loading');
     
    //start the ajax
    $.ajax({
        //this is the php file that processes the data
        url: "/ajax/filter-reports.php", 
         
        //GET method is used
        type: "GET",

        //pass the data         
        data: data,     
         
        //Do not cache the page
        cache: false,
         
        //success
        success: function(reports) {   
        	$('#outer-container').removeClass('filter-expanded');  
            feed.html(reports); 
            feed.removeClass('loading');
            loadThumbnails();
			updateNumReports();
            }       
    });
}; 	

function loadMoreReports(onPage) {
    feed = $('#report-feed-container');
	params = getFilterValues(); 
	params['on-page'] = onPage;
	params['num-reports'] = feed.find('.report').length;

	var data = '';
    for(var index in params) {
	  	data += index + "=" + params[index] + "&";
	} 

	//find the last list of reports (only one until "See more reports" is clicked)
    reportsList = feed.find('ul.reports').last();

    //insert temporary loading sign after current list
    reportsList.after("<div id='temp-loading' class='loading'></div>");

    //start the ajax
    $.ajax({
        //this is the php file that processes the data
        url: "/ajax/load-more-reports.php", 
         
        //GET method is used
        type: "GET",

        //pass the data         
        data: data,     
         
        //Do not cache the page
        cache: false,
         
        //success
        success: function(reports) { 
        	$('#temp-loading').remove();  
            reportsList.after(reports); 
            loadThumbnails();		               
			
			//rewrite feed count at top
			updateNumReports();

			//disable button if no more reports
			//console.log(reports.match('<li'));
			if (reports.match('<li') == null)
				disableMoreReportsButton();
            }       
    });				 
}		

function loadReportDetails(report) {
	//alert('ok');
	var detailSection = report.find('.detail-section'),
		reportId = report.attr('reportid'),
		obuoys = report.attr('hasbuoys'),
		otideStation = report.attr('hastide'),
		otimezone = report.attr('tz'),
		oreportTime = report.attr('reporttime'),
		oreporterId = report.attr('reporterid'),
		oimagePath = report.attr('imagepath');
		
	if (report.hasClass('collapsed')) {
		//console.log(report);
		report.removeClass('collapsed').addClass('expanded');	
		
		if (detailSection.hasClass('loaded')) {
			return;
		}

		detailSection.addClass('loading');	
	 	detailSection.load('/ajax/report-details.php?id=' + reportId,
	 		{
	 			buoys:obuoys,
	 			tideStation:otideStation,
	 			timezone:otimezone,
	 			reportTime:oreportTime,
	 			reporterId:oreporterId,
	 			imagePath:oimagePath
	 		}
	 		, function(){
				detailSection.removeClass('loading');
				detailSection.addClass('loaded');
	 		}
	 	)
	} else {
		report.removeClass('expanded').addClass('collapsed');
	}
}

function loadNewReport() {

	$('ul.reports').prepend("<li class=\"report loading\" id=\"new-report\"></li>");

    $.ajax({
        //this is the php file that processes the data
        url: "/ajax/new-report.php", 
         
        //GET method is used
        type: "GET",

        //Do not cache the page
        cache: false,
         
        //success
        success: function(newReport) {   
        	$('#new-report').replaceWith(newReport); 
        	$('.reports .report').first().click(function(){
        		$(this).toggleClass('expanded').toggleClass('collapsed');
        	}); 	
			loadThumbnails();	
			updateNumReports();	            		               
            }       
    });		 		
};			

function loadThumbnails(){
	$('.image-container.thumbnail-image img').each(function(elem){
		src = $(this).attr('realUrl');
		$(this).attr('src', src);
		$(this).parent('.thumbnail-image').removeClass('loading').addClass('loaded');
	});				
}

function updateNumReports(){
	numReportsElem = $('#report-feed-container').find('#numReports').first();
	numReports = $('#report-feed-container').find('.report').length;
	numReportsElem.text(numReports);
}

function disableMoreReportsButton(){
	$('#more-reports').addClass('disabled');
}
