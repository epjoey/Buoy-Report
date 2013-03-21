var BR = window.BR = {};

(function(){

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
			var parents = $(event.target).parent('.item'),
				stationId = $(parents[0]).attr('stationid');
			if (stationId) {
				this.$el.find("input.station-id").val(stationId);
				//console.log(this.$el);
				this.$el.submit();
			}
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
})();