// icheck line blue and green functions
$('input[type="checkbox"].line-blue, input[type="radio"].line-blue').each(function(){
	
	 var self = $(this),
      label = self.next(),
      label_text = label.text();

    label.remove();
	  
	self.iCheck({
      checkboxClass: 'icheckbox_line-blue',
      radioClass: 'iradio_line-blue',
      insert: '<div class="icheck_line-icon"></div>' + label_text
    });
	
});
$('input[type="checkbox"].line-green, input[type="radio"].line-green').each(function(){
	
	 var self = $(this),
      label = self.next(),
      label_text = label.text();

    label.remove();
	  
	self.iCheck({
      checkboxClass: 'icheckbox_line-green',
      radioClass: 'iradio_line-green',
      insert: '<div class="icheck_line-icon"></div>' + label_text
    });
	
});      
  



$(document).ready(function() {
$.fn.editable.defaults.ajaxOptions = {type: "GET"};

$('.editable').editable(); 

$('#data-table').DataTable( {
        dom: 'T<"clear">lfrtip'
		,aLengthMenu:[10,50,100,200,500,1000]

	} );
	


$("abbr.timeago").timeago();

$(".masked").inputmask();

});