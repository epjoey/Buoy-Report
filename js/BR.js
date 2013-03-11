var BR = window.BR = {};

(function(){

	//todo: max buoys per location, check if buoy is online.
	BR.BuoySelector = Backbone.View.extend({
		initialize: function() {
			this.trigger = $(options.trigger);
			this.container = $(options.container);
			this.trigger.click(function(event){
				this.container.toggle().addClass('loading').load(options.selectorUrl, function(response, status, xhr) {
					this.container.removeClass('loading');
				}.bind(this));
			}.bind(this));
			this.container.click(function(event){
				var clicked = $(event.target);
				var buoyEl = null;
				if (clicked.parent('.item').length) {
					buoyEl = $(clicked.parent('.item')[0]);
				} else if (clicked.hasClass('item')) {
					buoyEl = clicked;
				} else {
					return;
				}
				buoyId = buoyEl.attr('buoyid');
				buoyEl.addClass('loading');
				var statusEl = this.container.find('.status');
				$.ajax({
					data: {
						buoyid: buoyId,
						locationid: options.locationId
					},
					url: options.addBuoyUrl,
					type: 'POST',
					success: function(data, status, jqXHR) {
						if (data.success) {
							location.reload(true);


							//statusEl.text("Buoy " + buoyId + " has been set for this location.");
						}
						if (data.status == 'duplicate') {
							statusEl.text("Buoy " + buoyId + " is already set for this location.");
						}
						buoyEl.removeClass('loading');
					}
				});
			}.bind(this));
		}
	});

	BR.LocationAddBuoy = Backbone.View.extend({
		initialize: function() {
			new BR.BuoySelector({el:this.$el});
			new buoy.LocationAddBuoyForm({el:this.$el.find('#add-buoy-form')});
		},
		events: {
			'#enter-buoy':'onSubmit'
		},
		onSubmit: function() {

		}
	});
})();