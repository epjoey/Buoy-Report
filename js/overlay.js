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