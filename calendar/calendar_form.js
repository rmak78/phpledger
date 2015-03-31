// JavaScript Document

var ccWidth = 0;
var ccHeight = 0;

function setValue(){
	var f = document.calendarform;
	var date_selected = padString(f.selected_year.value, 4, "0") + "-" + padString(f.selected_month.value, 2, "0") + "-" + padString(f.selected_day.value, 2, "0");

	//not use for now
	//toggle = typeof(toggle) != 'undefined' ? toggle : true;

	if(typeof(window.parent.setValue) == "function")
		window.parent.setValue(f.objname.value, date_selected, false);
	else alert("Error! The calendar value cannot be set!");
}

function unsetValue(){
	var f = document.calendarform;
	f.selected_day.value = "00";
	f.selected_month.value = "00";
	f.selected_year.value = "0000";

	setValue();

	this.loading();
	f.submit();
}

function restoreValue(){
	var f = document.calendarform;
	var date_selected = padString(f.selected_year.value, 4, "0") + "-" + padString(f.selected_month.value, 2, "0") + "-" + padString(f.selected_day.value, 2, "0");

	if(typeof(window.parent.updateValue) == "function")
		window.parent.updateValue(f.objname.value, date_selected);
}

function selectDay(d){
	var f = document.calendarform;
	f.selected_day.value = d.toString();
	f.selected_month.value = f.m[f.m.selectedIndex].value;
	f.selected_year.value = f.y[f.y.selectedIndex].value;

	setValue();

	this.loading();
	f.submit();

	submitNow(f.selected_day.value, f.selected_month.value, f.selected_year.value);
}

function hL(E, mo){
	//clear last selected
	if(document.getElementById("select")){
		var selectobj = document.getElementById("select");
		selectobj.Id = "";
	}

	while (E.tagName!="TD"){
		E=E.parentElement;
	}

	E.Id = "select";
}

function selectMonth(m){
	var f = document.calendarform;
	f.selected_month.value = m;
}

function selectYear(y){
	var f = document.calendarform;
	f.selected_year.value = y;
}

function move(m, y){
	var f = document.calendarform;
	f.m.value = m;
	f.y.value = y;

	this.loading();
	f.submit();
}

function today(){
	var f = document.calendarform;
	f.m.value = this.today_month;
	f.y.value = this.today_year;

	this.loading();
	f.submit();
}

function closeMe(){
	window.parent.toggleCalendar(this.obj_name);
}

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

function loading(){
	if(this.ccWidth > 0 && this.ccHeight > 0){
		var ccobj = getObject('calendar-container');

		ccobj.style.width = this.ccWidth+'px';
		ccobj.style.height = this.ccHeight+'px';
	}

	document.getElementById('calendar-container').innerHTML = "<div id=\"calendar-body\"><div class=\"refresh\"><div align=\"center\" class=\"txt-container\">Refreshing Calendar...</div></div></div>";
	adjustContainer();
}

function submitCalendar(){
	this.loading();
	document.calendarform.submit();
}

function getObject(item){
	if(window.mmIsOpera) return(document.getElementById(item));
	if(document.all) return(document.all[item]);
	if(document.getElementById) return(document.getElementById(item));
	if(document.layers) return(document.layers[item]);
	return(false);
}

function adjustContainer(){
	var tc_obj = getObject('calendar-page');
	//var tc_obj = frm_obj.contentWindow.getObject('calendar-page');
	if(tc_obj != null){
		var div_obj = window.parent.document.getElementById('div_'+obj_name);

		if(tc_obj.offsetWidth > 0 && tc_obj.offsetHeight > 0){
			div_obj.style.width = tc_obj.offsetWidth+'px';
			div_obj.style.height = tc_obj.offsetHeight+'px';
			//alert(div_obj.style.width+','+div_obj.style.height);

			var ccsize = getObject('calendar-container');
			this.ccWidth = ccsize.offsetWidth;
			this.ccHeight = ccsize.offsetHeight;
		}
	}
}

function getCalendarParam(name){
	var f = document.calendarform;
	var obj_name = f.objname.value;

	if(window.parent.document.getElementById(obj_name+"_"+name) != null){
		return window.parent.document.getElementById(obj_name+"_"+name).value;
	}else return "";
}

function processTooltips(){
	//console.log("Processing tooltips "+current_year+" "+current_month);
	
	var ttd = myJSONParse(decodeURIComponent(htmlspecialchars_decode(getCalendarParam("ttd"))));
	var ttt = myJSONParse(htmlspecialchars_decode(getCalendarParam("ttt")));

	//yearly recursive
	for (var key in ttd[2]) {
	  if (ttd[2].hasOwnProperty(key)) {
		this_date = new Date(ttd[2][key]);
		this_date_str = pad(current_year, 4, "0")+''+pad(this_date.getMonth()+1, 2, "0")+''+pad(this_date.getDate(), 2, "0");
		this_tooltip = typeof(ttt[2][key]) != "undefined" ? ttt[2][key] : "";

		if((this_tooltip.substring(0,1) == '"' && this_tooltip.substring(this_tooltip.length-1) == '"') || (this_tooltip.substring(0,1) == "'" && this_tooltip.substring(this_tooltip.length-1) == "'")){
			this_tooltip = this_tooltip.substring(1, this_tooltip.length-1);
			this_tooltip = replaceAll("&#10;", String.fromCharCode(10), this_tooltip);
		}
		//alert(this_date_str+','+this_tooltip);
		if(this_tooltip != ""){
			//console.log(this_tooltip);
			var date_obj = document.getElementById(this_date_str);
			if(date_obj != null){
				var obj_list = date_obj.getElementsByTagName("div");
				if(obj_list[0] != null){
					//check if tooltip is already existed
					var spn_obj = obj_list[0].getElementsByTagName("span");

					if(spn_obj[0] != null){
						var alt_txt = spn_obj[0].getAttribute("alt");
						alt_txt += String.fromCharCode(10)+this_tooltip;
						spn_obj[0].setAttribute("alt", alt_txt);
						spn_obj[0].setAttribute("title", alt_txt);
						spn_obj[0].onclick = function() {showTitle(this);};
					}else{
						var info_obj = document.createElement("span");
						info_obj.setAttribute("alt", this_tooltip);
						info_obj.setAttribute("title", this_tooltip);
						info_obj.onclick = function() {showTitle(this);};
						info_obj.className = "calendartooltip";

						obj_list[0].insertBefore(info_obj, null);
					}
				}
				//console.log(date_obj.innerHTML);
			}
		}
	  }
	}

	//monthly recursive
	for (var key in ttd[1]) {
	  if (ttd[1].hasOwnProperty(key)) {
		this_date = new Date(ttd[1][key]);
		this_date_str = pad(current_year, 4, "0")+''+pad(current_month, 2, "0")+''+pad(this_date.getDate(), 2, "0");
		this_tooltip = typeof(ttt[1][key]) != "undefined" ? ttt[1][key] : "";

		if((this_tooltip.substring(0,1) == '"' && this_tooltip.substring(this_tooltip.length-1) == '"') || (this_tooltip.substring(0,1) == "'" && this_tooltip.substring(this_tooltip.length-1) == "'")){
			this_tooltip = this_tooltip.substring(1, this_tooltip.length-1);
			this_tooltip = replaceAll("&#10;", String.fromCharCode(10), this_tooltip);
		}
		//alert(this_date_str+','+this_tooltip);

		if(this_tooltip != ""){
			//console.log(this_tooltip);
			var date_obj = document.getElementById(this_date_str);
			if(date_obj != null){
				var obj_list = date_obj.getElementsByTagName("div");
				if(obj_list[0] != null){
					//check if tooltip is already existed
					var spn_obj = obj_list[0].getElementsByTagName("span");

					if(spn_obj[0] != null){
						var alt_txt = spn_obj[0].getAttribute("alt");
						alt_txt += String.fromCharCode(10)+this_tooltip;
						spn_obj[0].setAttribute("alt", alt_txt);
						spn_obj[0].setAttribute("title", alt_txt);
						spn_obj[0].onclick = function() {showTitle(this);};
					}else{
						var info_obj = document.createElement("span");
						info_obj.setAttribute("alt", this_tooltip);
						info_obj.setAttribute("title", this_tooltip);
						info_obj.onclick = function() {showTitle(this);};
						info_obj.className = "calendartooltip";

						obj_list[0].insertBefore(info_obj, null);
					}
				}
				//console.log(date_obj.innerHTML);
			}
		}
	  }
	}

	//no recursive
	for (var key in ttd[0]) {
		if (ttd[0].hasOwnProperty(key)) {
			this_date = new Date(ttd[0][key]);
			//alert(sp_dates[2][key]+","+this_date.getDate());
			this_date_str = pad(this_date.getFullYear(), 4, "0")+''+pad(this_date.getMonth()+1, 2, "0")+''+pad(this_date.getDate(), 2, "0");
			this_tooltip = typeof(ttt[0][key]) != "undefined" ? ttt[0][key] : "";

			if((this_tooltip.substring(0,1) == '"' && this_tooltip.substring(this_tooltip.length-1) == '"') || (this_tooltip.substring(0,1) == "'" && this_tooltip.substring(this_tooltip.length-1) == "'")){
				this_tooltip = this_tooltip.substring(1, this_tooltip.length-1);
				this_tooltip = replaceAll("&#10;", String.fromCharCode(10), this_tooltip);
			}
			//alert(this_date_str+','+this_tooltip);

			if(this_tooltip != ""){
				//console.log(this_tooltip);
				var date_obj = document.getElementById(this_date_str);
				if(date_obj != null){
					var obj_list = date_obj.getElementsByTagName("div");
					if(obj_list[0] != null){
						//check if tooltip is already existed
						var spn_obj = obj_list[0].getElementsByTagName("span");

						if(spn_obj[0] != null){
							var alt_txt = spn_obj[0].getAttribute("alt");
							alt_txt += String.fromCharCode(10)+this_tooltip;
							spn_obj[0].setAttribute("alt", alt_txt);
							spn_obj[0].setAttribute("title", alt_txt);
							spn_obj[0].onclick = function() {showTitle(this);};
						}else{
							var info_obj = document.createElement("span");
							info_obj.setAttribute("alt", this_tooltip);
							info_obj.setAttribute("title", this_tooltip);
							info_obj.onclick = function() {showTitle(this);};
							info_obj.className = "calendartooltip";

							obj_list[0].insertBefore(info_obj, null);
						}
					}
					//console.log(date_obj.innerHTML);
				}
			}
		}
	}
}

function pad(n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function replaceAll(find, replace, str) {
	return str.replace(new RegExp(find, 'g'), replace);
}

function myJSONParse(d){
	//only array is assume for now
	if(d != "" && d.length > 2){
		var tmp_d = d.substring(2, d.length-2);
		var v = tmp_d.split("],[");
		for(i=0; i<v.length; i++){
			var s = v[i];
			if(s == "")
				v[i] = new Array();
			else v[i] = s.split(",");
		}
	}else v = new Array();

	return v;
}

function showTitle(obj){
	alert(obj.getAttribute("title"));
}

function htmlspecialchars_decode (string, quote_style) {
  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Mateusz "loonquawl" Zalega
  // +      input by: ReverseSyntax
  // +      input by: Slawomir Kaniecki
  // +      input by: Scott Cariss
  // +      input by: Francois
  // +   bugfixed by: Onno Marsman
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
  // *     returns 1: '<p>this -> &quot;</p>'
  // *     example 2: htmlspecialchars_decode("&amp;quot;");
  // *     returns 2: '&quot;'
  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }
  string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');

  return string;
}

window.onload = function(){
	//adjustContainer();
	setTimeout("adjustContainer()", 1000);
	restoreValue();
	processTooltips();
};