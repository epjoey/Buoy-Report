function loadMoreReports(form) {
    feed = $('#report-feed-container');
	data = $(form).serialize(); 
	numReports = feed.find('.report').length;
    data = data + "&offset=" + numReports;
    
	//find the last list of reports (only one until "See more reports" is clicked)
    reportsList = feed.find('ul.reports').last();

    //insert temporary loading sign after current list
    reportsList.after("<div id='temp-loading' class='loading'></div>");

    //start the ajax
    $.ajax({
        url: "/ajax/load-more-reports.php", 
        type: "GET",
        data: data,
        cache: false,
        success: function(reports) { 
        	$('#temp-loading').remove();  
            reportsList.after(reports); 
            loadThumbnails();		               
			
			//rewrite feed count at top
			updateNumReports();

			//disable button if no more reports
			if (reports.match('<li') == null)
				$('#more-reports').addClass('disabled');
            }       
    });				 
}		

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

