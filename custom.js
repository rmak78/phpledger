$(document).ready(function() {
$.fn.editable.defaults.ajaxOptions = {type: "GET"};

$('.editable').editable(); 

$('#data-table').DataTable( {
        dom: 'T<"clear">lfrtip'
		,aLengthMenu:[10,50,100,200,500,1000]

	} );
	


$("abbr.timeago").timeago();



});