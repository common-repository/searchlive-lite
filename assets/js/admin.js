(function ($){
	"use strict";
	
    var SearchliveAdmin 	= {
		
		init: function(){
			var self = this;
			
			self.saveSettings();
		},
		
		saveSettings: function(){
			var button = $('.searchlive_settings_page .slive_save_btn');
			button.off().on('click', function(e){
				e.preventDefault();
				var thisBtn 	= jQuery(this);
				var form 		= thisBtn.closest('form');
				var setOptions 	= form.serialize();

				jQuery.post( 'options.php' , setOptions ).error(function(){}).success(function(){
					console.log('Saved');
				});

				return false;

			});	
		},
		
    };
	
	$(document).ready(function(){SearchliveAdmin.init();});

})(jQuery);