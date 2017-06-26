var timer;
var sys_path = window.location.protocol + "//" + window.location.host + "/";

function search(type,keyword){
	if(type=='user'){
		$.ajax({
		    url: sys_path+'/users/search',
		    cache: false,
		    type: 'POST',
		    dataType: 'json',
		    data: {keyword:keyword},
		    success: function (data) {
		    	console.log(data.User);
		    	var result = "";
		    	try {
			    	$.each(data, function(index, element) {
			    		result += '<span data-id="'+element.User.user_id+'">' + 'Employee ID:' + element.User.user_id+ ' Name:' + element.User.name+ '</span>';
					    // console.log(element.timeStamp); 
					});
					if(result == ""){
						jQuery("#result #result-data").html("No Result");
					}else{
			    		jQuery("#result #result-data").html(result);
					}
					jQuery("#result .loading").hide();
					jQuery("#result #result-data").show();
			  	}catch (e) {
			    	console.log(e);
			  	}
		    }
		});
	}
}

jQuery( document ).ready(function() {
    $('.searchUser').keyup(function(e) {
	    var keyword = $(this).val()
    	jQuery("#result").show();
    	jQuery("#result .loading").show();
    	jQuery("#result #result-data").hide();
	    clearTimeout(timer);
	    timer = setTimeout(function() {
	      search('user',keyword);
	    }, 1000);
	});

	$('#result').on('click','span',function(e){
		var id = $(this).attr('data-id');
		$('.searchUser').val(id);
		jQuery("#result").hide();
	});
});