var hideCalendarTimer = new Array();

function calendarTimer(objname){
	this.objname = objname;
	this.timers = new Array();
}

function toggleCalendar(objname, auto_hide, hide_timer){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		if (div_obj.style.visibility=="hidden") {
		  div_obj.style.visibility = 'visible';
		  document.getElementById(objname+'_frame').contentWindow.adjustContainer();

		  //auto hide if inactivities with calendar after open
		  if(auto_hide){
			  if(hide_timer < 3000) hide_timer = 3000; //put default 3 secs
			  prepareHide(objname, hide_timer);
		  }
		}else{
		  div_obj.style.visibility = 'hidden';
		}
	}
}

function showCalendar(objname){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		div_obj.style.visibility = 'visible';
		document.getElementById(objname+'_frame').contentWindow.adjustContainer();
	}
}

function hideCalendar(objname){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		div_obj.style.visibility = 'hidden';
	}
}

function prepareHide(objname, timeout){
	cancelHide(objname);

	var timer = setTimeout(function(){ hideCalendar(objname) }, timeout);

	var found = false;
	for(i=0; i<this.hideCalendarTimer.length; i++){
		if(this.hideCalendarTimer[i].objname == objname){
			found = true;
			this.hideCalendarTimer[i].timers[this.hideCalendarTimer[i].timers.length] = timer;
		}
	}

	if(!found){
		var obj = new calendarTimer(objname);
		obj.timers[obj.timers.length] = timer;

		this.hideCalendarTimer[this.hideCalendarTimer.length] = obj;
	}
}

function cancelHide(objname){
	for(i=0; i<this.hideCalendarTimer.length; i++){
		if(this.hideCalendarTimer[i].objname == objname){
			var timers = this.hideCalendarTimer[i].timers;
			for(n=0; n<timers.length; n++){
				clearTimeout(timers[n]);
			}
			this.hideCalendarTimer[i].timers = new Array();
			break;
		}
	}
}

function setValue(objname, d, submt){
	//compare if value is changed
	var changed = (document.getElementById(objname).value != d) ? true : false;

	updateValue(objname, d);

	var dp = document.getElementById(objname+"_dp").value;
	if(dp) toggleCalendar(objname);

	checkPairValue(objname, d);

	//calling calendar_onchanged script
	if(document.getElementById(objname+"_och").value != "" && changed) calendar_onchange(objname);

	if(typeof(submt) == "undefined") submt = true;
	
	if(submt){
		var date_array = document.getElementById(objname).value.split("-");
	
		tc_submitDate(objname, date_array[2], date_array[1], date_array[0]);
	}
}

function updateValue(objname, d){
	document.getElementById(objname).value = d;

	var dp = document.getElementById(objname+"_dp").value;
	if(dp == true){
		var date_array = d.split("-");

		var inp = document.getElementById(objname+"_inp").value;
		if(inp == true){

			document.getElementById(objname+"_day").value = padString(date_array[2].toString(), 2, "0");
			document.getElementById(objname+"_month").value = padString(date_array[1].toString(), 2, "0");
			document.getElementById(objname+"_year").value = padString(date_array[0].toString(), 4, "0");

			//check for valid day
			tc_updateDay(objname, date_array[0], date_array[1], date_array[2]);

		}else{
			if(date_array[0] > 0 && date_array[1] > 0 && date_array[2] > 0){
				//update date pane

				var myDate = new Date();
				myDate.setFullYear(date_array[0],(date_array[1]-1),date_array[2]);
				var dateFormat = document.getElementById(objname+"_fmt").value;

				var dateTxt = myDate.format(dateFormat);
			}else var dateTxt = "Select Date";

			document.getElementById("divCalendar_"+objname+"_lbl").innerHTML = dateTxt;
		}
	}
}

function tc_submitDate(objname, dvalue, mvalue, yvalue){
	var obj = document.getElementById(objname+'_frame');

	var year_start = document.getElementById(objname+'_year_start').value;
	var year_end = document.getElementById(objname+'_year_end').value;
	var dp = document.getElementById(objname+'_dp').value;

	var da1 = document.getElementById(objname+'_da1').value;
	var da2 = document.getElementById(objname+'_da2').value;
	var sna = document.getElementById(objname+'_sna').value;
	var aut = document.getElementById(objname+'_aut').value;
	var frm = document.getElementById(objname+'_frm').value;
	var tar = document.getElementById(objname+'_tar').value;
	var inp = document.getElementById(objname+'_inp').value;
	var fmt = document.getElementById(objname+'_fmt').value;
	var dis = document.getElementById(objname+'_dis').value;

	var pr1 = document.getElementById(objname+'_pr1').value;
	var pr2 = document.getElementById(objname+'_pr2').value;
	var prv = document.getElementById(objname+'_prv').value;
	var path = document.getElementById(objname+'_pth').value;

	var spd = document.getElementById(objname+'_spd').value;
	var spt = document.getElementById(objname+'_spt').value;

	var och = document.getElementById(objname+'_och').value;
	var str = document.getElementById(objname+'_str').value;
	var rtl = document.getElementById(objname+'_rtl').value;
	var wks = document.getElementById(objname+'_wks').value;
	var int = document.getElementById(objname+'_int').value;

	var hid = document.getElementById(objname+'_hid').value;
	var hdt = document.getElementById(objname+'_hdt').value;
	
	var tmz = document.getElementById(objname+'_tmz').value;

	obj.src = path+"calendar_form.php?objname="+objname.toString()+"&selected_day="+dvalue+"&selected_month="+mvalue+"&selected_year="+yvalue+"&year_start="+year_start+"&year_end="+year_end+"&dp="+dp+"&da1="+da1+"&da2="+da2+"&sna="+sna+"&aut="+aut+"&frm="+frm+"&tar="+tar+"&inp="+inp+"&fmt="+fmt+"&dis="+dis+"&pr1="+pr1+"&pr2="+pr2+"&prv="+prv+"&spd="+spd+"&spt="+spt+"&och="+och+"&str="+str+"&rtl="+rtl+"&wks="+wks+"&int="+int+"&hid="+hid+"&hdt="+hdt+"&tmz="+tmz;

	obj.contentWindow.submitNow(dvalue, mvalue, yvalue);
}

function tc_setDMY(objname, dvalue, mvalue, yvalue){
	var obj = document.getElementById(objname);
	obj.value = yvalue + "-" + mvalue + "-" + dvalue;

	tc_submitDate(objname, dvalue, mvalue, yvalue);
}

function tc_setDay(objname, dvalue){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");

	//check if date is not allow to select
	if(!isDateAllow(objname, dvalue, date_array[1], date_array[0]) || !checkSpecifyDate(objname, dvalue, date_array[1], date_array[0])){
		//alert("This date is not allow to select");
		restoreDate(objname);
	}else{
		if(isDate(dvalue, date_array[1], date_array[0])){
			tc_setDMY(objname, dvalue, date_array[1], date_array[0]);
		}else document.getElementById(objname+"_day").selectedIndex = date_array[2];
	}

	checkPairValue(objname, obj.value);

	//compare if value is changed
	var changed = (document.getElementById(objname).value != d) ? true : false;

	//calling calendar_onchanged script
	if(document.getElementById(objname+"_och").value != "" && changed) calendar_onchange(objname);
}

function tc_setMonth(objname, mvalue){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");

	//check if date is not allow to select
	if(!isDateAllow(objname, date_array[2], mvalue, date_array[0]) || !checkSpecifyDate(objname, date_array[2], mvalue, date_array[0])){
		//alert("This date is not allow to select");
		restoreDate(objname);
	}else{
		if(document.getElementById(objname+'_dp').value && document.getElementById(objname+'_inp').value){
			//update 'day' combo box
			date_array[2] = tc_updateDay(objname, date_array[0], mvalue, date_array[2]);
		}

		if(isDate(date_array[2], mvalue, date_array[0])){
			tc_setDMY(objname, date_array[2], mvalue, date_array[0]);
		}else document.getElementById(objname+"_month").selectedIndex = date_array[1];
	}

	checkPairValue(objname, obj.value);

	//compare if value is changed
	var changed = (document.getElementById(objname).value != d) ? true : false;

	//calling calendar_onchanged script
	if(document.getElementById(objname+"_och").value != "" && changed) calendar_onchange(objname);
}

function tc_setYear(objname, yvalue){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");

	//check if date is not allow to select
	if(!isDateAllow(objname, date_array[2], date_array[1], yvalue) || !checkSpecifyDate(objname, date_array[2], date_array[1], yvalue)){
		//alert("This date is not allow to select");
		restoreDate(objname);
	}else{
		if(document.getElementById(objname+'_dp').value && document.getElementById(objname+'_inp').value){
			//update 'day' combo box
			date_array[2] = tc_updateDay(objname, yvalue, date_array[1], date_array[2]);
		}

		if(isDate(date_array[2], date_array[1], yvalue)){
			tc_setDMY(objname, date_array[2], date_array[1], yvalue);
		}else document.getElementById(objname+"_year").value = date_array[0];
	}

	checkPairValue(objname, obj.value);

	//compare if value is changed
	var changed = (document.getElementById(objname).value != d) ? true : false;

	//calling calendar_onchanged script
	if(document.getElementById(objname+"_och").value != "" && changed) calendar_onchange(objname);
}

function yearEnter(e){
	var characterCode;

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){
		//if Enter is pressed, do nothing
		return true;
	}else return false;
}

// Declaring valid date character, minimum year and maximum year
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function is_leapYear(year){
	return (year % 4 == 0) ?
		!(year % 100 == 0 && year % 400 != 0)	: false;
}

function daysInMonth(month, year){
	var days = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	return (month == 2 && year > 0 && is_leapYear(year)) ? 29 : days[month-1];
}
/*
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31;
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   }
   return this
}
*/
function isDate(strDay, strMonth, strYear){
/*
	//bypass check date
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || day > daysInMonth(month, year)){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}*/
	return true
}

function isDateAllow(objname, strDay, strMonth, strYear){
	var da1 = document.getElementById(objname+"_da1").value;
	var da2 = document.getElementById(objname+"_da2").value;

	strDay = parseInt(parseFloat(strDay));
	strMonth = parseInt(parseFloat(strMonth));
	strYear = parseInt(parseFloat(strYear));

	if(strDay>0 && strMonth>0 && strYear>0){
		var this_date = new Date(strYear, strMonth-1, strDay);
		
		if(da1 != "" && da2 != ""){
			da1_date = new Date(da1);
			da2_date = new Date(da2);
			
			if(da1_date<=this_date && da2_date>=this_date){
				return true;
			}else{ 
				alert("Please choose a date between\n"+ da1 + " and " + da2);
				return false;
			}
		}else if(da1 != ""){
			da1_date = new Date(da1);
			if(da1_date<=this_date){
				return true;
			}else{ 
				alert("Please choose a date after " + da1);
				return false;
			}
		}else if(da2 != ""){
			da2_date = new Date(da2);
			if(da2_date>=this_date){
				return true;
			}else{ 
				alert("Please choose a date before " + da2);
				return false;
			}
		}
	}

	return true; //always return true if date not completely set
}

function restoreDate(objname){
	//get the store value
	var storeValue = document.getElementById(objname).value;
	var storeArr = storeValue.split('-', 3);

	//set it
	document.getElementById(objname+'_day').value = storeArr[2];
	document.getElementById(objname+'_month').value = storeArr[1];
	document.getElementById(objname+'_year').value = storeArr[0];
}

//----------------------------------------------------------------
// javascript date format function thanks to Jacob Wright
// http://jacwright.com/projects/javascript/date_format
// updated 2/8/2013 with an addition from Haravikk
// MIT Licensed!
// some modifications to match the calendar script
//----------------------------------------------------------------

// Simulates PHP's date function
Date.prototype.format = function(format) {
    var returnStr = '';
    var replace = Date.replaceChars;

    for (var i = 0; i < format.length; i++) {
			var curChar = format.charAt(i);
			if (i - 1 >= 0 && format.charAt(i - 1) == "\\") {
            returnStr += curChar;
        }
        else if (replace[curChar]) {
            returnStr += replace[curChar].call(this);
        } else if (curChar != "\\"){
            returnStr += curChar;
        }
    }
    return returnStr;
};

Date.replaceChars = {
    shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],

    // Day
    d: function() { return (this.getDate() < 10 ? '0' : '') + this.getDate(); },
    D: function() { return Date.replaceChars.shortDays[this.getDay()]; },
    j: function() { return this.getDate(); },
    l: function() { return Date.replaceChars.longDays[this.getDay()]; },
    N: function() { return this.getDay() + 1; },
    S: function() { return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th'))); },
    w: function() { return this.getDay(); },
    z: function() { var d = new Date(this.getFullYear(),0,1); return Math.ceil((this - d) / 86400000); }, // Fixed now
    // Week
    W: function() { var d = new Date(this.getFullYear(), 0, 1); return Math.ceil((((this - d) / 86400000) + d.getDay() + 1) / 7); }, // Fixed now
    // Month
    F: function() { return Date.replaceChars.longMonths[this.getMonth()]; },
    m: function() { return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1); },
    M: function() { return Date.replaceChars.shortMonths[this.getMonth()]; },
    n: function() { return this.getMonth() + 1; },
    t: function() { var d = new Date(); return new Date(d.getFullYear(), d.getMonth(), 0).getDate() }, // Fixed now, gets #days of date
    // Year
    L: function() { var year = this.getFullYear(); return (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)); },   // Fixed now
    o: function() { var d  = new Date(this.valueOf());  d.setDate(d.getDate() - ((this.getDay() + 6) % 7) + 3); return d.getFullYear();}, //Fixed now
    Y: function() { return this.getFullYear(); },
    y: function() { return ('' + this.getFullYear()).substr(2); },
    // Time
    a: function() { return this.getHours() < 12 ? 'am' : 'pm'; },
    A: function() { return this.getHours() < 12 ? 'AM' : 'PM'; },
    B: function() { return Math.floor((((this.getUTCHours() + 1) % 24) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600) * 1000 / 24); }, // Fixed now
    g: function() { return this.getHours() % 12 || 12; },
    G: function() { return this.getHours(); },
    h: function() { return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12); },
    H: function() { return (this.getHours() < 10 ? '0' : '') + this.getHours(); },
    i: function() { return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes(); },
    s: function() { return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds(); },
    u: function() { var m = this.getMilliseconds(); return (m < 10 ? '00' : (m < 100 ? '0' : '')) + m; },
    // Timezone
    e: function() { return "Not Yet Supported"; },
    I: function() {
        var DST = null;
            for (var i = 0; i < 12; ++i) {
                    var d = new Date(this.getFullYear(), i, 1);
                    var offset = d.getTimezoneOffset();

                    if (DST === null) DST = offset;
                    else if (offset < DST) { DST = offset; break; }
					else if (offset > DST) break;
            }
            return (this.getTimezoneOffset() == DST) | 0;
        },
    O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
    P: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':00'; }, // Fixed now
    T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
    Z: function() { return -this.getTimezoneOffset() * 60; },
    // Full Date/Time
    c: function() { return this.format("Y-m-d\\TH:i:sP"); }, // Fixed now
    r: function() { return this.toString(); },
    U: function() { return this.getTime() / 1000; }
};

function padString(stringToPad, padLength, padString) {
	if (stringToPad.length < padLength) {
		while (stringToPad.length < padLength) {
			stringToPad = padString + stringToPad;
		}
	}else {}
/*
	if (stringToPad.length > padLength) {
		stringToPad = stringToPad.substring((stringToPad.length - padLength), padLength);
	} else {}
*/
	return stringToPad;
}

function tc_updateDay(objname, yearNum, monthNum, daySelected){
	//var totalDays = (monthNum > 0) ? daysInMonth(monthNum, yearNum) : 31;
	var totalDays = (monthNum > 0 && yearNum > 0) ? daysInMonth(monthNum, yearNum) : ((monthNum > 0) ? daysInMonth(monthNum, 2008) : 31);

	var dayObj = document.getElementById(objname+"_day");
	//var prevSelected = dayObj.value;

	if(dayObj.options[0].value == 0 || dayObj.options[0].value == "")
		dayObj.length = 1;
	else dayObj.length = 0;

	for(d=1; d<=totalDays; d++){
		var newOption = document.createElement("OPTION");

		newOption.text = d;
		newOption.value = d;

		dayObj.options[d] = new Option(newOption.text, padString(newOption.value, 2, "0"));
	}

	if(daySelected > totalDays)
		dayObj.value = padString(totalDays, 2, "0");
	else dayObj.value = padString(daySelected, 2, "0");

	return dayObj.value;
}

function checkPairValue(objname, d){
	var dp1 = document.getElementById(objname+"_pr1").value;
	var dp2 = document.getElementById(objname+"_pr2").value;

	var this_value = document.getElementById(objname).value;
	//var this_time2 = Date.parse(this_value)/1000;
	//var this_time1 = Date.parse(this_value.replace(/-/g,'/'))/1000;

	var this_dates = this_value.split('-');
	var this_time = new Date(this_dates[0], this_dates[1]-1, this_dates[2]).getTime()/1000;

	//implementing dp2
	if(dp1 != "" && document.getElementById(dp1) != null){ //imply to date_pair1
		//set date pair value to date selected
		document.getElementById(dp1+"_prv").value = d;

		var dp1_value = document.getElementById(dp1).value;
		//var dp1_time = Date.parse(dp1_value)/1000;
		//var dp1_time = Date.parse(dp1_value.replace(/-/g,'/'))/1000;

		var dp1_dates = dp1_value.split('-');
		var dp1_time = new Date(dp1_dates[0], dp1_dates[1]-1, dp1_dates[2]).getTime()/1000;

		if(this_time < dp1_time || this_value == "0000-00-00"){
			//set self date pair value to null
			document.getElementById(objname+"_prv").value = "";

			tc_submitDate(dp1, "00", "00", "0000");
		}else{
			//var date_array = document.getElementById(dp1).value.split("-");
			tc_submitDate(dp1, dp1_dates[2], dp1_dates[1], dp1_dates[0]);
		}
	}

	//implementing dp1
	if(dp2 != "" && document.getElementById(dp2) != null){ //imply to date_pair2
		//set date pair value to date selected
		document.getElementById(dp2+"_prv").value = d;

		var dp2_value = document.getElementById(dp2).value;
		//var dp2_time = Date.parse(dp2_value)/1000;
		//var dp2_time = Date.parse(dp2_value.replace(/-/g,'/'))/1000;

		var dp2_dates = dp2_value.split('-');
		var dp2_time = new Date(dp2_dates[0], dp2_dates[1]-1, dp2_dates[2]).getTime()/1000;

		if(this_time > dp2_time || this_value == "0000-00-00"){
			//set self date pair value to null
			document.getElementById(objname+"_prv").value = "";

			tc_submitDate(dp2, "00", "00", "0000");
		}else{
			//var date_array = document.getElementById(dp2).value.split("-");
			tc_submitDate(dp2, dp2_dates[2], dp2_dates[1], dp2_dates[0]);
		}
	}
}

function checkSpecifyDate(objname, strDay, strMonth, strYear){
	var spd = urldecode(document.getElementById(objname+"_spd").value);
	var spt = document.getElementById(objname+"_spt").value;

	//alert(spd);
	var sp_dates;

	if(typeof(JSON) != "undefined"){
		sp_dates = JSON.parse(spd);
	}else{
/*
		//only array is assume for now
		if(spd != "" && spd.length > 2){
			var tmp_spd = spd.substring(2, spd.length-2);
			//alert(tmp_spd);
			var sp_dates = tmp_spd.split("],[");
			for(i=0; i<sp_dates.length; i++){
				//alert(sp_dates[i]);
				var tmp_str = sp_dates[i]; //.substring(1, sp_dates[i].length-1);
				if(tmp_str == "")
					sp_dates[i] = new Array();
				else sp_dates[i] = tmp_str.split(",");
			}
		}else sp_dates = new Array();
*/
		sp_dates = myJSONParse(spd);
	}
	/*
	for(i=0; i<sp_dates.length; i++){
		for(j=0; j<sp_dates[i].length; j++){
			alert(sp_dates[i][j]);
		}
	}
	*/

	var found = false;

	for (var key in sp_dates[2]) {
	  if (sp_dates[2].hasOwnProperty(key)) {
		this_date = new Date(sp_dates[2][key]*1000);
		//alert(sp_dates[2][key]+","+this_date.getDate());
		if(this_date.getDate() == parseInt(parseFloat(strDay)) && (this_date.getMonth()+1) == parseInt(parseFloat(strMonth))){
			found = true;
			break;
		}
	  }
	}

	if(!found){
		for (var key in sp_dates[1]) {
		  if (sp_dates[1].hasOwnProperty(key)) {
			this_date = new Date(sp_dates[1][key]*1000);
			//alert(sp_dates[2][key]+","+this_date.getDate());
			if(this_date.getDate() == parseInt(parseFloat(strDay))){
				found = true;
				break;
			}
		  }
		}
	}

	if(!found){
		var choose_date = new Date(strYear, strMonth-1, strDay);
		var choose_time = choose_date.getTime()/1000;

		for (var key in sp_dates[0]) {
		  if (sp_dates[0].hasOwnProperty(key)) {
			//alert(key + " -> " + p[key]);
			if(choose_time == sp_dates[0][key]){
				found = true;
				break;
			}
		  }
		}
	}

	switch(spt){
		case 0:
		default:
			//date is disabled
			if(found){
				alert("You cannot choose this date");
				return false;
			}
			break;
		case 1:
			//other dates are disabled
			if(!found){
				alert("You cannot choose this date");
				return false;
			}
			break;
	}
	return true;
}

function urldecode (str) {
	return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

function calendar_onchange(objname){
	//you can modify or replace the code below
	var fc = document.getElementById(objname+"_och").value;
	//alert("Date has been set to "+obj.value);
	eval(urldecode(fc));
}

function focusCalendar(objname){
	var obj = document.getElementById("container_"+objname);
	if(obj != null){
		obj.style.zIndex = 999;
	}
}

function unFocusCalendar(objname, zidx){
	var obj = document.getElementById("container_"+objname);
	if(obj != null){
		obj.style.zIndex = zidx;
	}
}

function myJSONParse(d){
	//only array is assume for now
	if(d != "" && d.length > 2){
		var tmp_d = d.substring(2, d.length-2);
		//alert(tmp_spd);
		var v = tmp_d.split("],[");
		for(i=0; i<v.length; i++){
			//alert(sp_dates[i]);
			var s = v[i]; //.substring(1, sp_dates[i].length-1);
			if(s == "")
				v[i] = new Array();
			else v[i] = s.split(",");
		}
	}else v = new Array();

	return v;
}