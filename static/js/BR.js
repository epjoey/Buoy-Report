var BR = window.BR = {};

(function($){

	//todo: max buoys per location, check if buoy is online.
	BR.LocationAddBuoyForm = Backbone.View.extend({
		initialize: function() {
		},
		events: {
			"click .add-existing" : "showExistingBuoys",
			"click .station-list" : "selectExistingBuoy",
			"submit" : "onSubmit"
		},
		showExistingBuoys: function(event) {
			var container = this.$el.find(".station-list");
			container.addClass('loading');
			$.ajax(this.options.existingStationsUrl, {
				success: function(html) {
					container.html(html).removeClass('loading');
				}
			});
		},
		selectExistingBuoy: function(event) {
			var id   = $(event.target).parent('.item').find('.id').html(),
				name = $(event.target).parent('.item').find('.name').html();
			
			this.$el.find("input.station-id").val(id);
			this.$el.find("input.station-name").val(name);

			//this.$el.submit();
		},
		onSubmit: function(event) {
			//this.$el.serialize();
		}
	});

	BR.LocationRemoveBuoysForm = Backbone.View.extend({
		initialize: function() {
		},
		events: {
			'submit':'onSubmit'
		},
		onSubmit: function(event) {
		}
	});

	BR.locationForecastLinks = {
		doFcLinkAjax: function(newUrl) {
			linkContainer = $('#fc-link-container');
			if (newUrl != '') {
				data = {url:newUrl}
			} else {
				data = {}
			}
			if (linkContainer.hasClass('loaded') && newUrl == '') return;
			linkContainer.addClass('loading');
			linkContainer.load('/controllers/location/forecast-links.php?info=forecast&locationid=<?=$this->location->id?>', 
				data,			
				function(){
					linkContainer.removeClass('loading').addClass('loaded');
				}
			);
		},

		cancelDeleteLinks: function() {
			$('.delete-link-check').hide();
			$('#delete-link-cancel').hide();
			$('#add-fc-link-form').show();
			$('#delete-link-btn').text('Delete links').removeClass('ready');
		},

		deleteCheckedLinks: function (links) {
			elems = $('.delete-link-check:checked');
			links = [];

			elems.each(function(){
				links.push($(this).val());
			});

			$.ajax({
				url: "/controllers/location/forecast-links.php?info=deletelinks&locationid=<?=$this->location->id?>",
				type: "GET",
				data: { links : links },
				cache: false,
				success: function() {
					elems.closest('.fc-link').remove();
					BR.locationForecastLinks.cancelDeleteLinks();
				} 
			});
		}
	}

    BR.loadMoreReports = function(params, onSuccess){
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

    BR.updateNumReports = function(){
        numReports = $('#report-feed-container .report').length;
        $('#numReports').text(numReports);
    }

})(jQuery);

