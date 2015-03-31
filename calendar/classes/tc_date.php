<?php
//*************************************
// Date handling class for tc_calendar
// for php version higher than 5.3.0
// written by TJ @triconsole
//*************************************

require_once('tc_date_main.php');

// Set the default timezone identifier; List of all identifiers: http://www.php.net/manual/en/timezones.php
// Example:
// date_default_timezone_set('Europe/Bucharest');
#date_default_timezone_set('UTC'); //for php servers >5.4 that require setting timezone before calling date()

class tc_date extends tc_date_main{
	var $compatible;

	function tc_date(){
		//check if we should use DateTime that comes with 5.3.0 and later
		if (version_compare(PHP_VERSION, '5.3.0') <= 0) {
			$this->compatible = false;
		}else $this->compatible = true;

		if(!$this->compatible){
			$this->tc_date_main();
		}else{
			$this->mydate = new DateTime('now');
		}
	}

	function getDayOfWeek($cdate = ""){
		if(!$this->compatible){
			return tc_date_main::getDayOfWeek($cdate);
		}else{
			if(($cdate != "" && $this->validDate($cdate)) || $cdate == ""){
				$tmp_date = ($cdate != "") ? new DateTime($cdate) : $this->mydate;
				return $tmp_date->format('w');
			}else return "";
		}
	}


	function getWeekNumber($cdate = ""){
		if(!$this->compatible){
			return tc_date_main::getWeekNumber($cdate);
		}else{
			if(($cdate != "" && $this->validDate($cdate)) || $cdate == ""){
				$tmp_date = ($cdate != "") ? new DateTime($cdate) : $this->mydate;
				return $tmp_date->format('W');
			}else return "";
		}
	}

	function setDate($sdate){
		if(!$this->compatible){
			tc_date_main::setDate($sdate);
		}else{
			if(tc_date_main::validDate($sdate))
				$this->mydate = new DateTime($sdate);
		}
	}

	function getDate($format = "Y-m-d", $cdate = ""){
		if(!$this->compatible){
			return tc_date_main::getDate($format, $cdate);
		}else{
			if(($cdate != "" && $this->validDate($cdate)) || $cdate == ""){
				$tmp_date = ($cdate != "") ? new DateTime($cdate) : $this->mydate;
				return $tmp_date->format($format);
			}else return "";
		}
	}

	function setTimestamp($stime){
		if(!$this->compatible){
			tc_date_main::setTimestamp($stime);
		}else{
			$this->mydate->setTimestamp($stime);
		}
	}

	function getTimestamp($cdate = ""){
		if(!$this->compatible){
			return tc_date_main::getTimestamp($cdate);
		}else{
			if(($cdate != "" && $this->validDate($cdate)) || $cdate == ""){
				$tmp_date = ($cdate != "") ? new DateTime($cdate) : $this->mydate;
				return $tmp_date->getTimestamp();
			}else return 0;
		}
	}

	function getDateFromTimestamp($stime, $format = 'Y-m-d'){
		if($stime){
			if(!$this->compatible){
				return tc_date_main::getDateFromTimestamp($stime, $format);
			}else{
				$tmp_date = new DateTime();
				$tmp_date->setTimestamp($stime);
				return $tmp_date->format($format);
			}
		}else return "";
	}

	function addDay($format = "Y-m-d", $timespan, $cdate = ""){
		if(!$this->compatible){
			return tc_date_main::addDay($format, $timespan, $cdate);
		}else{
			$timespan = "P".$timespan."D";
			return $this->addDate($format, $timespan, $cdate);
		}
	}

	function addMonth($format = "Y-m-d", $timespan, $cdate = ""){
		if(!$this->compatible){
			return tc_date_main::addMonth($format, $timespan, $cdate);
		}else{
			$timespan = "P".$timespan."M";
			return $this->addDate($format, $timespan, $cdate);
		}
	}

	function addYear($format = "Y-m-d", $timespan, $cdate = ""){
		if(!$this->compatible){
			return tc_date_main::addYear($format, $timespan, $cdate);
		}else{
			$timespan = "P".$timespan."Y";
			return $this->addDate($format, $timespan, $cdate);
		}
	}

	function addDate($format = "Y-m-d", $timespan, $cdate = ""){
		if($this->compatible){
			$tmp_date = ($cdate != "") ? new DateTime($cdate) : $this->mydate;
			$tmp_date->add(new DateInterval($timespan));
			return $tmp_date->format($format);
		}else return "0000-00-00";
	}

	//return the number of day different between date1 and date2
	//if date1 omitted use set date
	function differentDate($date2, $date1 = ""){
		if(!$this->compatible){
			return tc_date_main::differentDate($date2, $date1);
		}else{
			$date1 = ($date1 != "") ? $date1 : $this->getDate('Y-m-d');

			$date1 = new DateTime($date1);
			$date2 = new DateTime($date2);
			$interval = $date1->diff($date2, true);
			return $interval->format('%a');
		}
	}

	//check if date1 is before date2
	//if date1 omitted use set date
	function dateBefore($date2, $date1 = "", $equal = true){
		if(!$this->compatible){
			return tc_date_main::dateBefore($date2, $date1, $equal);
		}else{
			$date1 = ($date1 != "") ? $date1 : $this->getDate('Y-m-d');

			$date1 = new DateTime($date1);
			$date2 = new DateTime($date2);
			return ($equal) ? $date1<=$date2 : $date1<$date2;
		}
	}

	//check if date1 is after date2
	//if date1 omitted use set date
	function dateAfter($date2, $date1 = "", $equal = true){
		if(!$this->compatible){
			return tc_date_main::dateAfter($date2, $date1, $equal);
		}else{
			$date1 = ($date1 != "") ? $date1 : $this->getDate('Y-m-d');

			$date1 = new DateTime($date1);
			$date2 = new DateTime($date2);
			return ($equal) ? $date1>=$date2 : $date1>$date2;
		}
	}
}
?>