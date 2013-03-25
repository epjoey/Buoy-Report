function loadMoreReports(params, onSuccess) {
    $.ajax({
        url: "/ajax/report/load-more.php" + params.queryStr, 
        type: "GET",
        data: params,
        cache: false,
        success: function(reports) {
            if (onSuccess) {
                onSuccess(reports);
            }
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

