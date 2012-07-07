//this function is not being used 6.4.12
function filterReports(form) {    

    feed = $(document).find('#report-feed');
    data = form.serialize();
	
    //console.log(data);

    //show the loading sign
    feed.children().fadeOut(60);
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
            feed.hide();
            feed.html(reports); 
            feed.removeClass('loading');
            feed.fadeIn(60);
            loadThumbnails();
			updateFilterNote();
        }       
    });
}; 	

function loadMoreReports(form) {
    feed = $('#report-feed-container');
	data = $(form).serialize(); 
	numReports = feed.find('.report').length;
    data = data + "&offset=" + numReports;
    //console.log(data);

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
				$('#more-reports').addClass('disabled');
            }       
    });				 
}		

function loadReportDetails(report) {
	var detailSection = report.find('.detail-section'),
		reportId = report.attr('reportid'),
		obuoys = report.attr('hasbuoys'),
		otideStation = report.attr('hastide'),
		otimezone = report.attr('tz'),
		oreportTime = report.attr('reporttime'),
		oreporterId = report.attr('reporterid'),
		oimagePath = report.attr('imagepath');

    //console.log(reportId);
		
	if (report.hasClass('collapsed')) {
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

function updateFilterNote(){
	updateNumReports();
}

function updateNumReports(){
    numReports = $('#report-feed-container .report').length;
    $('#numReports').text(numReports);
}

