// Function to avoid IE error when console.log is called
if ( ! window.console ) console = { log: function(){} };

jQuery.noConflict();
jQuery(document).ready(function($){
	$('a.json-call').on('click',function(e){
		e.preventDefault();
		var that = $(this).attr('data-id');
		var data_fn = $(this).attr('data-fn');
		var data_fol = $(this).attr('data-fol');
		var dataObj = {
	       'action': 	'wplf_json',
	       'fn':     	data_fn,
	       'following': data_fol 	 
		}
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			data: dataObj,
		   	beforeSend: function(x) {
		    	if(x && x.overrideMimeType) {
		            x.overrideMimeType("application/json;charset=UTF-8");
		        }
		    },
			url: ajaxObj.ajaxurl,
			success:function(data){
				if(data.check !== false && data.fn !== 'error' ) {
					$('.'+that).toggleClass('followed').empty().append(data.text).attr('data-fn',data.fn);
				} else {
					$.colorbox({
						width: '468px',
						scrolling: false,
						html: '<div class="warning">'+data.text+'</div>'
					});
				}
				console.dir(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
			}
		});
	});
});
