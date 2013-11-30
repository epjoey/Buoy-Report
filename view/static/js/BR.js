var BR = window.BR = {};

(function($){

	//todo: max buoys per location, check if buoy is online.
	BR.LocationAddStationForm = Backbone.View.extend({
		events: {
			"click .add-existing" : "showExistingStations",
			"click .station-list" : "selectExistingStation",
			"submit" : "onSubmit"
		},
		showExistingStations: function(event) {
			var container = this.$el.find(".station-list");
			container.addClass('loading');
			$.ajax(this.options.existingStationsUrl, {
				success: function(html) {
					container.html(html).removeClass('loading').addClass('visible');
				}
			});
		},
		selectExistingStation: function(event) {
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

	BR.LocationRemoveStationForm = Backbone.View.extend({
		initialize: function() {
			this.$el.find("input[type=submit]").hide();
			this.$el.find("input[type=checkbox]").hide();
			this.$el.find("span.button").show();
		},
		events: {
			'click span.submit':'onSpanSubmit'
		},
		onSpanSubmit: function(event) {
			$(event.target).parent().find("input[type=checkbox]").attr('checked', true);
			this.$el.submit();
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


	BR.reportFeed = {
		onLoad: function(feed) {
			this.updateNumReports(feed);
			this.loadImages(feed);
		},
		paginate: function(params, onSuccess) {
			$.ajax({
				url: "/controllers/report/feed.php", 
				type: "GET",
				data: params,
				cache: false,
				success: function(reports) {
					if (onSuccess) {
						onSuccess(reports);
					}
				}       
			});	    		
		},
		updateNumReports: function(feed) {
			numReports = feed.find('.report').length;
			$('#numReports').text(numReports);    		
		},
		loadImages: function(feed) {
			$(feed).find('.image-container img').each(function(){ 
				BR.images.lazyLoad($(this));
			});
		}

	}
	BR.images = {
		lazyLoad: function(img) {
			var img = $(img);
			if (!img) {
				return;
			}
			img.attr('src', img.attr('imgsrc'));
		}
	}

})(jQuery);

