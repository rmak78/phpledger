<?php
require_once('classes/tc_date.php');
require_once('classes/tc_calendar.php');
require_once('calendar_functions.php');

$thispage = $_SERVER['PHP_SELF'];

//timezone var, need to setup before any other tasks
$timezone = getParameter("tmz");

if(!$timezone) $timezone = date_default_timezone_get();
@date_default_timezone_set($timezone);


$cdate = new tc_date();
$today = $cdate->getDate();

$sld = getParameter("selected_day", "number", 0);
$slm = getParameter("selected_month", "number", 0);
$sly = getParameter("selected_year", "number", 0);

$year_start = getParameter("year_start", "number", 0);
$year_end = getParameter("year_end", "number", 0);

$startDate = getParameter("str", "number", 0);

$time_allow1 = getParameter("da1");
$time_allow2 = getParameter("da2");

$ta1_set = ($time_allow1 != "") ? true : false;
$ta2_set = ($time_allow2 != "") ? true : false;

$show_not_allow = getParameter("sna", "boolean", true);

$auto_submit = getParameter("aut", "boolean", false);
$form_name = getParameter("frm");
$target_url = getParameter("tar");

$show_input = getParameter("inp", "boolean", true);
$date_format = getParameter("fmt", "text", 'd-M-Y');

$dsb_txt = getParameter("dis");

$date_pair1 = getParameter("pr1");
$date_pair2 = getParameter("pr2");

$date_pair_value = getParameter("prv");
$path = getParameter("pth");

$sp_dates = (isset($_REQUEST["spd"])) ? @tc_calendar::check_json_decode(htmlspecialchars_decode($_REQUEST["spd"], ENT_QUOTES)) : array(array(), array(), array());
//echo("<br />".htmlspecialchars_decode($_REQUEST["spd"], ENT_QUOTES));
//print_r($sp_dates);

$sp_type = getParameter("spt", "number", 0);

$tc_onchanged = getParameter("och");
$rtl = getParameter("rtl");

$show_weeks = getParameter("wks", "boolean", false);
$interval = getParameter("int", "number", 1);

$auto_hide = getParameter("hid", "number", 0);
$auto_hide_time = getParameter("hdt", "number", 1000);

//check year to be select in case of date_allow is set
if(!$show_not_allow){
  if ($ta1_set && $cdate->validDate($time_allow1)) $year_start = $cdate->getDate('Y', $time_allow1);
  if ($ta2_set && $cdate->validDate($time_allow2)) $year_end = $cdate->getDate('Y', $time_allow2);
}


if(isset($_REQUEST["m"]))
	$m = getParameter("m", "number");
else{
	if($slm != "00"){
		$m = $slm;
	}else{
		if($ta2_set && $year_end > 0){
			//compare which one is more
			$year_allow2 = $cdate->getDate('Y', $time_allow2);
			if($year_allow2 >= $year_end){
				//use time_allow2
				$m = ($cdate->dateBefore($time_allow2)) ? $cdate->getDate("m") : $cdate->getDate('m', $time_allow2);
			}else{
				//use year_end
				$m = ($year_end > $cdate->getDate("Y")) ? $cdate->getDate("m") : 12;
			}
		}elseif($ta2_set){
			$m = ($cdate->dateBefore($time_allow2)) ? $cdate->getDate("m") : $cdate->getDate('m', $time_allow2);
		}elseif($year_end > 0){
			$m = ($year_end > $cdate->getDate("Y")) ? $cdate->getDate("m") : 12; //date('m')
		}else{ 
			$m = $cdate->getDate("m");
		}
	}
}

if($m < 1 && $m > 12) $m = $cdate->getDate("m");

$m = str_pad($m, 2, "0", STR_PAD_LEFT);

$cyr = ($sly != "0000") ? true : false;
if($sly && $sly < $year_start) $sly = $year_start;
if($sly && $sly > $year_end) $sly = $year_end;

if(isset($_REQUEST["y"]))
	$y = getParameter("y", "number");
else
	$y = ($cyr) ? $sly : $cdate->getDate("Y");

if($y <= 0) $y = $cdate->getDate("Y");

// ensure m-y fits date allow range
if (!$show_not_allow) {
  if ($ta1_set) {
    $m1 = $cdate->getDate('m', $time_allow1);
    $y1 = $cdate->getDate('Y', $time_allow1);
    if ($y == $y1 && (int)$m < (int)$m1) $m = $m1;
  }
  if ($ta2_set) {
    $m2 = $cdate->getDate('m', $time_allow2);
    $y2 = $cdate->getDate('Y', $time_allow2);
    if ($y == $y2 && (int)$m > (int)$m2) $m = $m2;
  }
}

$objname = getParameter("objname");
$dp = getParameter("dp", "boolean");

$cobj = new tc_calendar("");
$cobj->setDate($sld, $slm, $sly);
$cobj->startDate($startDate);
$cobj->dsb_days = explode(",", $dsb_txt);
$cobj->time_allow1 = $time_allow1;
$cobj->time_allow2 = $time_allow2;

$version = $cobj->version;
$check_version = $cobj->check_new_version;

$cobj->setYearInterval($year_start, $year_end);
$cobj->setTimezone($timezone); //set for further usage, nothing for now

//check and show default calendar month and year on valid range of date_allow
if(!isset($_REQUEST["m"])){
	if($time_allow1 != ""){
		//get date of time allow1
		if($cdate->validDate($time_allow1)){	
			//check valid if today is after date_allow1
			if(!$cdate->dateAfter($time_allow1, $today)){
				//reset default calendar display
				$y = $cdate->getDate('Y', $time_allow1);
				$m = $cdate->getDate('m', $time_allow1);
			}
		}
	}
}

$year_start = $cobj->year_start;
$year_end = $cobj->year_end;

//check year display in valid range
if($y >= $year_end) $y = $year_end;
if($y <= $year_start) $y = $year_start;

$total_thismonth = $cobj->total_days($m, $y);

if($m == 1){
	$previous_month = 12;
	$previous_year = $y-1;
}else{
	$previous_month = $m-1;
	$previous_year = $y;
}

if($m == 12){
	$next_month = 1;
	$next_year = $y+1;
}else{
	$next_month = $m+1;
	$next_year = $y;
}

$total_lastmonth = $cobj->total_days($previous_month, $previous_year);

$firstdate = $cdate->getDayOfWeek($y."-".$m."-1"); //first date of month, 0 (for Sunday) through 6 (for Saturday)

if($firstdate == $startDate){
	//skip last month
	$startwrite = $total_lastmonth+1;
}elseif($firstdate < $startDate){
	$startwrite = $total_lastmonth - (6-($startDate-$firstdate));
}else{
	$startwrite = $total_lastmonth - ($firstdate - $startDate - 1);
}

//--------------------------------
//prepare the calendar in array
//--------------------------------
$calendar_rows = array();
$week_rows = array(); //collection for week number, $week_rows[$row][$week_number] = counter

$dayinweek_counter = 0;
$row_count = 0;

//write previous month
for($day=$startwrite; $day<=$total_lastmonth; $day++){
	$calendar_rows[$row_count][] = array($day, "", "othermonth", "");
	$dayinweek_counter++;

	$prev_m = $m-1;
	$prev_d = $y."-".$prev_m."-".$day;

	$wknum = $cdate->getWeekNumber($prev_d);
	if(!isset($week_rows[$row_count][$wknum])){
		$week_rows[$row_count][$wknum] = 1;
	}else $week_rows[$row_count][$wknum] = $week_rows[$row_count][$wknum]+1;
}

$pvMonthTime = $previous_year."-".$previous_month."-".$total_lastmonth;

//check lastmonth is on allowed date
if($ta1_set && !$show_not_allow){
	if($cdate->dateBefore($pvMonthTime, $time_allow1)){
		$show_previous = true;
	}else $show_previous = false;
}else $show_previous = true; //always show when not set

$date_num = $cdate->getDayOfWeek(($previous_year."-".$previous_month."-".$total_lastmonth));
if(($startDate == 0 && $date_num == 6) || ($startDate > 0 && $date_num == $startDate-1) && $startwrite<$total_lastmonth){
	if(isset($calendar_rows[0])) $row_count++;
}

$dp_time = ($date_pair_value) ? $date_pair_value : "";

$select_days = array();
if($sld>0 && $slm>0 && $sly>0){
	$sldate = "$sly-$slm-$sld";

	for($i=0; $i<$interval; $i++){
		$this_day = $cdate->addDay("Y-m-d", $i, $sldate);
		$select_days[] = $this_day;
	}
}

//write current month
for($day=1; $day<=$total_thismonth; $day++){
	$date_str = $y."-".str_pad($m, 2, "0", STR_PAD_LEFT)."-".str_pad($day, 2, "0", STR_PAD_LEFT);

	$date_num = $cdate->getDayOfWeek($date_str);
	$day_txt = $cdate->getDate('D', $date_str);

	//$currentTime = $cdate->getTimestamp($y."-".$m."-".$day);
	
	$htmlClass = array();

	$is_today = ($cdate->differentDate($date_str) == 0) ? 1 : 0; //$is_today = $currentTime - strtotime($today);

	$htmlClass[] = ($is_today) ? "today" : "general";

	$dateLink = true;

	//check date allowed
	if($ta1_set && $ta2_set){
		//both date specified
		$dateLink = ($cdate->dateBefore($date_str, $time_allow1) && $cdate->dateAfter($date_str, $time_allow2)) ? true : false;
	}elseif($ta1_set){
		//only date 1 specified
		$dateLink = $cdate->dateBefore($date_str, $time_allow1) ? true : false;
	}elseif($ta2_set){
		//only date 2 specified
		$dateLink = $cdate->dateAfter($date_str, $time_allow2) ? true : false;
	}else{
		//no date allow specified, assume show all
		$dateLink = true;
	}

	if($dateLink){
		//check for disable days
		if(in_array(strtolower($day_txt), $cobj->dsb_days) !== false){
			$dateLink = false;
		}
	}

	//check specific date
	if($dateLink){		
		if(is_array($sp_dates) && sizeof($sp_dates) > 0){
			//check if it is current date
			$sp_found = false;

			//check on yearly recursive
			if(isset($sp_dates[2]) && is_array($sp_dates[2])){				
				foreach($sp_dates[2] as $sp_time){
					if($sp_time != ""){
						$sp_time_md = (int)$cdate->getDate('md', $sp_time); //date('md', $sp_time);
						$this_md = (int)$cdate->getDate('md', $date_str); //date('md', $currentTime);
						if($sp_time_md == $this_md){
							$sp_found = true;
							break;
						}
					}
				}
			}

			//check on monthly recursive
			if(isset($sp_dates[1]) && is_array($sp_dates[1]) && !$sp_found){				
				foreach($sp_dates[1] as $sp_time){
					if($sp_time != "" && $sp_time > 0){
						$sp_time_d = (int)$cdate->getDate('d', $sp_time); //date('d', $sp_time);
						if($sp_time_d == $day){
							$sp_found = true;
							break;
						}
					}
				}
			}

			//check on no recursive
			if(isset($sp_dates[0]) && is_array($sp_dates[0]) && !$sp_found){
				$sp_found = in_array($date_str, $sp_dates[0]);
			}

			switch($sp_type){
				case 0:
				default:
					//disabled specific and enabled others
					$dateLink = ($sp_found) ? false : true;
					break;
				case 1:
					//enabled specific and disabled others
					$dateLink = ($sp_found) ? true : false;
					break;
			}
		}
	}

	if($date_pair_value){
		//check date_pair1 &  2
		if($date_pair1 && $date_pair_value != "0000-00-00" && $cdate->dateAfter($date_pair_value, $date_str) && (($slm>0 && $sld>0 && $sly>0) && $cdate->dateBefore("$sly-$slm-$sld", $date_str))){ //set date only after date_pair1
			if(!in_array("select", $htmlClass))
				$htmlClass[] = "select";
		}

		if($date_pair2 && $date_pair_value != "0000-00-00" && $cdate->dateBefore($date_pair_value, $date_str) && (($slm>0 && $sld>0 && $sly>0) && $cdate->dateAfter("$sly-$slm-$sld", $date_str))){ //set date only before date_pair2
			if(!in_array("select", $htmlClass))
				$htmlClass[] = "select";
		}
	}

	$htmlClass[] = strtolower($day_txt);

	if($dateLink){
		if(in_array($date_str, $select_days) && !in_array("select", $htmlClass)){
			$htmlClass[] = "select";
		}

		//date with link
		$class = implode(" ", $htmlClass);

		$calendar_rows[$row_count][] = array($day, "javascript:selectDay('".str_pad($day, 2, "0", STR_PAD_LEFT)."');", $class, "$y".str_pad($m, 2, "0", STR_PAD_LEFT).str_pad($day, 2, "0", STR_PAD_LEFT));
	}else{
		$htmlClass[] = "disabledate";
		$class = implode(" ", $htmlClass);

		//date without link
		$calendar_rows[$row_count][] = array($day, "", $class, "$y".str_pad($m, 2, "0", STR_PAD_LEFT).str_pad($day, 2, "0", STR_PAD_LEFT));
	}
	if(($startDate == 0 && $date_num == 6) || ($startDate > 0 && $date_num == $startDate-1)){
		$row_count++;

		$dayinweek_counter = 0;
	}else $dayinweek_counter++;

	$wknum = $cdate->getWeekNumber(($y."-".$m."-".$day));

	if(!isset($week_rows[$row_count][$wknum])){
		$week_rows[$row_count][$wknum] = 1;
	}else $week_rows[$row_count][$wknum] = $week_rows[$row_count][$wknum]+1;
}

//write next other month
$write_end_days = (6-$dayinweek_counter)+1;
if($write_end_days > 0){
	for($day=1; $day<=$write_end_days; $day++){
		$calendar_rows[$row_count][] = array($day, "", "othermonth", "");

		$wknum = $cdate->getWeekNumber($cdate->addMonth("Y-m-d", 1, ($y."-".$m."-".$day))); //date('W', mktime(0,0,0, $m+1, $day, $y));
		if(!isset($week_rows[$row_count][$wknum])){
			$week_rows[$row_count][$wknum] = 1;
		}else $week_rows[$row_count][$wknum] = $week_rows[$row_count][$wknum]+1;
	}
	 $row_count++;
}

//write fulfil row to 6 rows
for($day=$row_count; $day<6; $day++){
	$tmpday = $write_end_days+1;
	for($f=$tmpday; $f<=($tmpday+6); $f++){
		$calendar_rows[$row_count][] = array($f, "", "othermonth", "");

		$wknum = $cdate->getWeekNumber($cdate->addMonth("Y-m-d", 1, ($y."-".$m."-".$f))); //date('W', mktime(0,0,0, $m+1, $f, $y));

		if(!isset($week_rows[$row_count][$wknum])){
			$week_rows[$row_count][$wknum] = 1;
		}else $week_rows[$row_count][$wknum] = $week_rows[$row_count][$wknum]+1;
	}
	$write_end_days += 6;
}

//check next month is on allowed date
if($ta2_set && !$show_not_allow){
	$nxMonthTime = $next_year."-".$next_month."-1";	
	if($cdate->dateAfter($nxMonthTime, $time_allow2)){
		$show_next = true;
	}else $show_next = false;
}else $show_next = true; //always show when not set

$wan_enabled = 0;
$new_version = 0;
if($wan_enabled = @fsockopen("www.google.com", 80, $errno, $errstr, 1)){
	if($check_version && $wan_enabled){
		if(function_exists("file_get_contents")){
			$new_version = @file_get_contents("http://www.triconsole.com/php/tc_calendar_version.php?v=".$version);
		}
	}
}
elseif(function_exists("file_get_contents")){
	$ctx = stream_context_create(array('http' => array('timeout' => 1)));
	$wan_enabled = @file_get_contents("http://www.google.com",null,$ctx,0,1);
	if($check_version && $wan_enabled){
		if(function_exists("file_get_contents")){
		$new_version = @file_get_contents("http://www.triconsole.com/php/tc_calendar_version.php?v=".$version);
		}
	}
}
define("L_ABOUT", "<b>PHP Datepicker Calendar</b><br />Version: <b>".strval($version)."</b>".($new_version ? "<br /><b><font color=\"red\">Update available <a href=\"$WEB_SUPPORT\" target=\"_blank\">here</a> !</font></b>" : "<br />")."<div class=\"fb-like\" data-href=\"https://www.facebook.com/DatePicker\" data-send=\"false\" data-layout=\"button_count\" data-show-faces=\"false\" data-font=\"tahoma\" ref=\"std_about_info\"></div><br />&copy;2006-".$cdate->getDate("Y")." <b><a href=\"$WEB_SUPPORT\" target=\"_blank\" title=\"http://triconsole.com\">$AUTHOR</a></b><p>Server Timezone: $timezone<br /><span id=\"timecontainer\">".$cdate->getDate("Y-m-d H:i:s")."</span></p>");

//date in about panel is for testing date with setup timezone
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if($rtl) echo(" dir=\"rtl\""); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TriConsole.com - PHP Calendar Date Picker</title>
<link href="calendar.css" rel="stylesheet" type="text/css" />
<script language="javascript">
<!--
var today_month = "<?php echo($cdate->getDate('m')); ?>";
var today_year = "<?php echo($cdate->getDate('Y')); ?>";
var obj_name = "<?php echo($objname); ?>";
var current_month = "<?php echo($m);?>";
var current_year = "<?php echo($y);?>";

var this_time = "<?php echo(date("F d, Y H:i:s", time())); ?>";
//-->
</script>
<script type="text/javascript" src="calendar_form.js"></script>
<script type="text/javascript">
<!--
function submitNow(dvalue, mvalue, yvalue){
	<?php
	//write auto submit script
	if($auto_submit){
		echo("if(yvalue>0 && mvalue>0 && dvalue>0){\n");
		if($form_name){
			//submit value by post form
			echo("window.parent.document.".$form_name.".submit();\n");
		}elseif($target_url){
			//submit value by get method
			echo("var date_selected = yvalue + \"-\" + mvalue + \"-\" + dvalue;\n");
			echo("window.parent.location.href='".$target_url."?".$objname."='+date_selected;\n");
		}
		echo("}\n");
	}
	?>
};
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<span id="calendar-page" class="font">
<?php if($wan_enabled){ ?>
	<div id="fb-root"></div>
    <script>
		window.fbAsyncInit = function() {
			FB.init({
			  appId: '674148172599839',
			  xfbml: true
			});
		  };
		  (function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol +
			  '//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
		  }());
	</script>
<?php } ?>
    <div id="calendar-header" align="center">
        <div style="float: left;" id="info">
        	<img src="images/<?php echo($new_version ? "version_info.gif" : "about.png"); ?>" width="9" height="9" border="0" id="info_icon" />
            <div id="about"><?php echo(L_ABOUT); ?></div>
            <script type="text/javascript" src="calendar_servertime.js"></script>
			<script type="text/javascript">
            new showLocalTime("timecontainer", "server-php", 0, "long")
            </script>
        	<script type="text/javascript">
			<!--
			var timeoutID = new Array();

			var obj = document.getElementById("info_icon");
			obj.onmouseover = function(){ displayAbout(); }
			obj.onmouseout = function(){ hideAbout(); }

			var obj = document.getElementById("about");
			obj.onmouseover = function(){ displayAbout(true); }
			obj.onmouseout = function(){ hideAbout(); }

			function displayAbout(flag){				
				var obj = document.getElementById("about");

				var this_height = obj.style.height;

				if(typeof(flag) == "undefined" || (flag === true && (this_height != "1px" && this_height != ""))){
					cancelTimer();
	
					//obj.style.display = "block";
					obj.style.height = "auto";
					obj.style.border = "1px solid #191970";
					obj.style.backgroundColor = "#F8F8FF";
				}
			}
			function hideAbout(){
				var obj = document.getElementById("about");

				this.timeoutID[this.timeoutID.length] = window.setTimeout(function(){
					obj.style.border = "none";
					//obj.style.display = "none";
					obj.style.height = "1px";
					obj.style.backgroundColor = "";
					}
					, 500);
			}
			function cancelTimer(){
				for(i=0; i<this.timeoutID.length; i++){
					var timers = this.timeoutID[i];
					clearTimeout(timers);
				}
				this.timeoutID = new Array();
			}
			//-->
			</script>
        </div>
        <?php if($dp && !$auto_hide){ ?>
        <div style="float: right;" class="closeme"><a href="javascript:closeMe();"><img src="images/close.gif" border="0" alt="Close" title="Close" /></a></div>
        <?php } ?>

        <?php
		if(sizeof($cobj->warning_msgs)>0){
			echo("<div id=\"calendar-alert\">".implode(", ", $cobj->warning_msgs)."</div>");
		}
		?>

        <form id="calendarform" name="calendarform" method="post" action="<?php echo($thispage);?>">
          <table align="center" cellpadding="1" cellspacing="0">
          	 <tr>
            	<td align="right"><select name="m" onchange="javascript:submitCalendar();" class="font">
				  <?php
                  $monthnames = $cobj->getMonthNames();
                  for($f=1; $f<=sizeof($monthnames); $f++){
                    $selected = ($f == (int)$m) ? " selected='selected'" : "";
                    echo("<option value=\"".str_pad($f, 2, "0", STR_PAD_LEFT)."\"".$selected.">".$monthnames[$f-1]."</option>");
                  }
                  ?>
                  </select></td><td align="left"><select name="y" onchange="javascript:submitCalendar();" class="font">
                  <?php
              	  $thisyear = $cdate->getDate('Y');
				  
                  //write year options
                  for($year=$year_end; $year>=$year_start; $year--){
                    $selected = ($year == $y) ? " selected='selected'" : "";
                    echo("<option value=\"".$year."\"".$selected.">".$year."</option>");
                  }
                  ?>
                  </select>
                </td>
             </tr>
           </table>
	    	<input name="selected_day" type="hidden" id="selected_day" value="<?php echo($sld);?>" />
            <input name="selected_month" type="hidden" id="selected_month" value="<?php echo($slm);?>" />
            <input name="selected_year" type="hidden" id="selected_year" value="<?php echo($sly);?>" />
            <input name="year_start" type="hidden" id="year_start" value="<?php echo($cobj->year_start_input);?>" />
            <input name="year_end" type="hidden" id="year_end" value="<?php echo($cobj->year_end_input);?>" />
            <input name="objname" type="hidden" id="objname" value="<?php echo($objname);?>" />
            <input name="dp" type="hidden" id="dp" value="<?php echo($dp);?>" />

            <input name="da1" type="hidden" id="da1" value="<?php echo($time_allow1);?>" />
            <input name="da2" type="hidden" id="da2" value="<?php echo($time_allow2);?>" />
            <input name="sna" type="hidden" id="sna" value="<?php echo($show_not_allow);?>" />
            <input name="aut" type="hidden" id="aut" value="<?php echo($auto_submit);?>" />
            <input name="frm" type="hidden" id="frm" value="<?php echo($form_name);?>" />
            <input name="tar" type="hidden" id="tar" value="<?php echo($target_url);?>" />
            <input name="inp" type="hidden" id="inp" value="<?php echo($show_input);?>" />
            <input name="fmt" type="hidden" id="fmt" value="<?php echo($date_format);?>" />
            <input name="dis" type="hidden" id="dis" value="<?php echo($dsb_txt);?>" />

            <input name="pr1" type="hidden" id="pr1" value="<?php echo($date_pair1);?>" />
            <input name="pr2" type="hidden" id="pr2" value="<?php echo($date_pair2);?>" />
            <input name="prv" type="hidden" id="prv" value="<?php echo($date_pair_value);?>" />
            <input name="pth" type="hidden" id="pth" value="<?php echo($path);?>" />

            <input name="spd" type="hidden" id="spd" value="<?php echo(htmlspecialchars($cobj->check_json_encode($sp_dates), ENT_QUOTES));?>" />
            <input name="spt" type="hidden" id="spt" value="<?php echo($sp_type);?>" />

            <input name="och" type="hidden" id="och" value="<?php echo(urldecode($tc_onchanged));?>" />

            <input name="str" type="hidden" id="str" value="<?php echo($startDate);?>" />
            <input name="rtl" type="hidden" id="rtl" value="<?php echo($rtl);?>" />
            <input name="wks" type="hidden" id="wks" value="<?php echo($show_weeks);?>" />
            <input name="int" type="hidden" id="int" value="<?php echo($interval);?>" />

            <input name="hid" type="hidden" id="hid" value="<?php echo($auto_hide);?>" />
            <input name="hdt" type="hidden" id="hdt" value="<?php echo($auto_hide_time);?>" />
            
            <input name="tmz" type="hidden" id="tmz" value="<?php echo($timezone);?>" />
      </form>
	</div>
    <div id="calendar-container">
        <div id="calendar-body">
        <table border="0" cellspacing="1" cellpadding="0" align="center" class="font">
            <?php
            $day_headers = array_values($cobj->getDayHeaders());

            echo("<tr>");

			if ($show_weeks) echo("<td align=\"center\" class=\"header wk-hdr\"><div>".$cobj->week_hdr."</div></td>");

			//write calendar day header
            foreach($day_headers as $dh){
                echo("<td align=\"center\" class=\"header\"><div>".$dh."</div></td>");
            }
            echo("</tr>");

			for($row=0; $row<sizeof($calendar_rows); $row++){
				echo("<tr>");

				if ($show_weeks){
					asort($week_rows[$row]);

					//get week number with highest member
					$cw_keys = array_keys($week_rows[$row]);

					echo("<td align=\"center\" class=\"wk\"><div>".$cw_keys[(sizeof($cw_keys)-1)]."</div></td>");
				}

				foreach($calendar_rows[$row] as $column){
					$this_day = isset($column[0]) ? $column[0] : "";
					$this_link = isset($column[1]) ? $column[1] : "";
					$this_class = isset($column[2]) ? $column[2] : "";
					$this_id = isset($column[3]) ? $column[3] : "";

					$id_str = ($this_id) ? " id=\"".$this_id."\"" : "";

					if($this_link){
						echo("<td".$id_str." align=\"center\" class=\"".$this_class."\"><div><a href=\"".$this_link."\">".$this_day."</a></div></td>");
					}else{
						echo("<td".$id_str." align=\"center\" class=\"".$this_class."\"><div>".$this_day."</div></td>");
					}
				}
				echo("</tr>");
			}
        ?>
        </table>
        </div>

		<?php
        if(($previous_year >= $year_start || $next_year <= $year_end) && ($show_previous || $show_next)){
        ?>
        <div id="calendar-footer">
            <div style="float: <?php echo($rtl ? "right" : "left"); ?>;" class="btn">
            <?php
            if($previous_year >= $year_start && $show_previous){
            ?><a href="javascript:move('<?php echo(str_pad($previous_month, 2, "0", STR_PAD_LEFT));?>', '<?php echo($previous_year);?>');"><img src="images/btn_<?php echo($rtl ? "next" : "previous"); ?>.gif" width="16" height="16" border="0" alt="Previous" title="Previous" /></a>
            <?php
            }else echo("&nbsp;");
            ?>
            </div>
            <div style="float: <?php echo($rtl ? "left" : "right"); ?>;" class="btn">
            <?php
            if($next_year <= $year_end && $show_next){
            ?><a href="javascript:move('<?php echo(str_pad($next_month, 2, "0", STR_PAD_LEFT));?>', '<?php echo($next_year);?>');"><img src="images/btn_<?php echo($rtl ? "previous" : "next"); ?>.gif" width="16" height="16" border="0" alt="Next" title="Next" /></a>
            <?php
            }else echo("&nbsp;");
            ?>
            </div>
            <div class="links">
                <?php
                $footer_links = array();

                if($cobj->validTodayDate() && ($m != $cdate->getDate('m') || $y != $cdate->getDate('Y')))
                    $footer_links[] = "<a href=\"javascript:today();\" class=\"txt\" alt=\"Today\" title=\"Today\">Today</a>";

                if($sld>0 && $slm>0 && $sly>0)
                    $footer_links[] = "<a href=\"javascript:unsetValue();\" class=\"txt\" alt=\"Unset\" title=\"Unset\">Unset</a>";

                if(sizeof($footer_links)>0){
                    echo(implode(" | ", $footer_links));
                }
                ?>
            </div>
        </div>
        <?php } ?>
	</div>
</span>
</body>
</html>