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

function insertOverlay(content) {
    $(content).insertAfter('#wrapper');
    $('input.required').first().focus();
    $(content).find('form').first().validate();     
}

function loginOverlay(error,rel) {
    data = {
        error: error,
        rel: rel
    }

    $.ajax({
        //this is the php file that processes the data
        url: "/ajax/login-form.php", 
         
        //GET method is used
        type: "GET",

        //pass the data         
        data: data,     
         
        //Do not cache the page
        cache: false,
         
        //success
        success: function(form) {   
            overlay = "<div id='overlay-container'><div class='overlay'>" + form + "</div></div>";
            insertOverlay(overlay);
            $('input.required').first().focus();
        }       
    });
}

var Overlay = Backbone.View.extend({
    initialize: function() {
    },
    show: function() {
        this.$el.insertAfter('#wrapper');
    }
});
