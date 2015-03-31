
</div><!-- close row -->
</div><!-- close container -->
<footer>
<center> PHP Ledger Accounting System <a href="http://phpledger.com/"> phpLedger </a> </center>
</footer>
<script type="text/javascript">

$(document).ready(function() {
$('#data-table').DataTable( {
        dom: 'T<"clear">lfrtip'
		,aLengthMenu:[10,50,100,200,500,1000]

	} );
$('.date-picker').datepicker({
    format: 'mm-dd-yyyy',
    startDate: '-30d',
    autoclose: true,
    endDate: '+0d' // there's no convenient "right now" notation yet
}); 
} );
</script>
 
</body>

</html>