var timer;
var sys_path = window.location.protocol + "//" + window.location.host + "/";


function exportTableToCSV($table, filename) {

    var $rows = $table.find('tr:has(td),tr:has(th)'),

        tmpColDelim = String.fromCharCode(11), 
        tmpRowDelim = String.fromCharCode(0), 

        colDelim = '","',
        rowDelim = '"\r\n"',

        csv = '"' + $rows.map(function (i, row) {
            var $row = $(row), $cols = $row.find('td,th');

            return $cols.map(function (j, col) {
                var $col = $(col), text = $col.text();

                return text.replace(/"/g, '""');

            }).get().join(tmpColDelim);

        }).get().join(tmpRowDelim)
            .split(tmpRowDelim).join(rowDelim)
            .split(tmpColDelim).join(colDelim) + '"',


        csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        if (window.navigator.msSaveBlob) { 
            window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
        } 
        else {
            $(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' }); 
        }
}


function search(type,keyword){
	// for getting the users data
	if(type=='user'){
		$.ajax({
		    url: sys_path+'users/search',
		    cache: false,
		    type: 'POST',
		    dataType: 'json',
		    data: {keyword:keyword},
		    success: function (data) {
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
	}else if(type=='userTable'){
		$.ajax({
		    url: sys_path+'users/search2',
		    cache: false,
		    type: 'POST',
		    dataType: 'json',
		    data: {keyword:keyword},
		    success: function (data) {
		    	console.log(data);
		    }
		});
	}
}

function setDateToday(){
	var d = new Date();

	var month = d.getMonth()+1;
	var day = d.getDate();

	var output = d.getFullYear() + '-' +
	    (month<10 ? '0' : '') + month + '-' +
	    (day<10 ? '0' : '') + day;

	$(".datepicker").val(output);
}

function getDateToday(){
	var d = new Date();

	var month = d.getMonth()+1;
	var day = d.getDate();

	var output = d.getFullYear() + '-' +
	    (month<10 ? '0' : '') + month + '-' +
	    (day<10 ? '0' : '') + day;

	return output;
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

	$('.search').keyup(function(e) {
		var keyword = $(this).val()
		clearTimeout(timer);
	    timer = setTimeout(function() {
	      search('userTable',keyword);
	    }, 1000);
	});

	// setDateToday();

	$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });

	$("#export").on('click', function (event) {
	    exportTableToCSV.apply(this, [$('#printTable'), 'inventory_report_'+getDateToday()+'.csv']);
	});


	$("#btnExport").click(function(e) {
	    e.preventDefault();

	    //getting data from table
	    var data_type = 'data:application/csv;charset=utf-8,';
	    var table_div = document.getElementById('printTable');
	    var table_html = table_div.outerHTML.replace(/ /g, '%20');

	    var a = document.createElement('a');
	    a.href = data_type + ', ' + table_html;
	    a.download = 'inventory_report_' + getDateToday() + '.xls';
	    a.click();
  	});


});