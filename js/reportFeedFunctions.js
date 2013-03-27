function loadMoreReports(params, onSuccess) {
    $.ajax({
        url: "/controllers/report/load-more.php" + params.queryStr, 
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


function updateFilterNote(){
	updateNumReports();
}

function updateNumReports(){
    numReports = $('#report-feed-container .report').length;
    $('#numReports').text(numReports);
}

