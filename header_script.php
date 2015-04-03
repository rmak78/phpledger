<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Load CSS Files -->
<link href='<?php echo SITE_ROOT; ?>calendar/calendar.css' rel='stylesheet' type='text/css' />
<link href='<?php echo SITE_ROOT; ?>stylesheet.css' rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>css/bootstrap.min.css"/> 
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>css/bootstrap-theme.min.css"/> 
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>css/bootstrap-editable.css"/>
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>css/dataTables.bootstrap.css" />
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>/css/dataTables.tableTools.min.css" /> 
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>/css/datepicker.css" /> 
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>/css/select2.css" /> 

<!-- Loading Java Scripts -->
  
<!-- jQuery & Bootstrap  -->
<script src="<?php echo SITE_ROOT; ?>js/jquery.min.js"></script>
<script src="<?php echo SITE_ROOT; ?>js/bootstrap.min.js"></script>
<!-- jQuery Select2 plugin -->
<script src="<?php echo SITE_ROOT; ?>js/select2.js"></script>
<!-- jQuery Time Ago plugin -->
<script src="<?php echo SITE_ROOT; ?>js/jquery.timeago.js"></script>
<!-- jQuery Editable Plugin -->
<script src="<?php echo SITE_ROOT; ?>js/bootstrap-editable.min.js"></script>
<!-- jQuery DataTables plugin -->
<script src="<?php echo SITE_ROOT; ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo SITE_ROOT; ?>js/dataTables.bootstrap.js"></script>
<script src="<?php echo SITE_ROOT; ?>js/dataTables.tableTools.min.js"></script>

<!-- Calender Widget -->
<script language='javascript' src='<?php echo SITE_ROOT; ?>calendar/calendar.js'></script>
<script type="text/javascript">
$(window).load(function() {
	$(".loader").fadeOut("slow");
})
</script>
</head><body>
<style>
.loader {
	position: fixed;
	left: 0px;
	top: 52px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url('<?php echo SITE_ROOT; ?>images/page-loader.gif') 50% 50% no-repeat rgb(249,249,249);
}</style>
<div class="loader"></div>
<div class="container-fluid"><div class="row-fluid">