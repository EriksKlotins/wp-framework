	$('select.control-type').change(function(){

	    //console.log($(this).val());
	    var val = $(this).val();
	    var nr = $(this).attr('nr');
	    if (val == 'Select' || val == 'Multiselect')
	    {
	        $('select[name="customfield-source-'+nr+'"]').removeClass('hidden');
	    }
	    else
	    {
	        $('select[name="customfield-source-'+nr+'"]').addClass('hidden');
	    
	    }
	});