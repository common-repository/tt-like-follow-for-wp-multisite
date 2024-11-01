jQuery.noConflict();
jQuery(document).ready(function($){
	$('.toggleRadio').on('change',function(e){
		var trg = $(this).attr('id').split('_');
		var vle = $(this).val();
		var tri = '.'+trg[0]+'_'+trg[1];
		if(trg[2] == 'no') {
			$(tri).slideDown(function(){
				$(tri+'_no').slideUp();
			});
		} else {
			$(tri).slideUp(function(){
				$(tri+'_no').slideDown();
			});
		}
	});
	$('a.json-call').on('click',function(e){
		e.preventDefault();
		var that = $(this);
		var data_fn = that.attr('data-fn');
		var data_fol = that.attr('data-fol');
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
			url: ajaxurl,
			success:function(data){
				//console.log(data);
				if(data.check !== false) that.toggleClass('followed').empty().append(data.text).attr('data-fn',data.fn);
			},
			error: function(errorThrown){
				//console.log(errorThrown);
			}
		});
	});
});
