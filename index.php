<?php
// PHP Ledger Starting Point
require_once('functions.php');
$include_file = "";
$path ="";
if (isset($_GET['route'])) {
$path = $_GET['route'];
} else {
	if (isset($_POST['route'])) {
	$path = $_POST['route'];
	}
}
if($path <> "") { // Checks if file really exists before including it
	$include_file = "./".$path.".php";
	if(!file_exists($include_file)) {
		$include_file = "includes/page-parts/content-404.php";
	}
		
}

?>
<?php include_once('includes/page-parts/header.php'); ?>
<?php include_once('includes/page-parts/top-nav.php'); ?>
<?php include_once('includes/page-parts/sidebar.php'); ?>
<?php include_once('includes/page-parts/content-top.php'); ?>
<?php 
		if($include_file <> "") {
			include($include_file);
		}  else {			
		 include('includes/page-parts/content-default.php');  
		}

 ?>

<?php include_once('includes/page-parts/content-bottom.php'); ?>
<?php include_once('includes/page-parts/footer.php'); ?>