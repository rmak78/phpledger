
</div><!-- close row -->
</div><!-- close container -->
<footer>
<center> PHP Ledger Accounting System <a href="http://phpledger.com/"> phpLedger </a> </center>
</footer>
<script type="text/javascript">

$(document).ready(function() {
$.fn.editable.defaults.ajaxOptions = {type: "GET"};
/*  TODO: fix this source array  */
 var countries = [];
    $.each({"BD": "Bangladesh", "BE": "Belgium", "BF": "Burkina Faso", "BG": "Bulgaria", "BA": "Bosnia and Herzegovina", "BB": "Barbados", "WF": "Wallis and Futuna", "BL": "Saint Bartelemey", "BM": "Bermuda", "BN": "Brunei Darussalam", "BO": "Bolivia", "BH": "Bahrain", "BI": "Burundi", "BJ": "Benin", "BT": "Bhutan", "JM": "Jamaica", "BV": "Bouvet Island", "BW": "Botswana", "WS": "Samoa", "BR": "Brazil", "BS": "Bahamas", "JE": "Jersey", "BY": "Belarus", "O1": "Other Country", "LV": "Latvia", "RW": "Rwanda", "RS": "Serbia", "TL": "Timor-Leste", "RE": "Reunion", "LU": "Luxembourg", "TJ": "Tajikistan", "RO": "Romania", "PG": "Papua New Guinea", "GW": "Guinea-Bissau", "GU": "Guam", "GT": "Guatemala", "GS": "South Georgia and the South Sandwich Islands", "GR": "Greece", "GQ": "Equatorial Guinea", "GP": "Guadeloupe", "JP": "Japan", "GY": "Guyana", "GG": "Guernsey", "GF": "French Guiana", "GE": "Georgia", "GD": "Grenada", "GB": "United Kingdom", "GA": "Gabon", "SV": "El Salvador", "GN": "Guinea", "GM": "Gambia", "GL": "Greenland", "GI": "Gibraltar", "GH": "Ghana", "OM": "Oman", "TN": "Tunisia", "JO": "Jordan", "HR": "Croatia", "HT": "Haiti", "HU": "Hungary", "HK": "Hong Kong", "HN": "Honduras", "HM": "Heard Island and McDonald Islands", "VE": "Venezuela", "PR": "Puerto Rico", "PS": "Palestinian Territory", "PW": "Palau", "PT": "Portugal", "SJ": "Svalbard and Jan Mayen", "PY": "Paraguay", "IQ": "Iraq", "PA": "Panama", "PF": "French Polynesia", "BZ": "Belize", "PE": "Peru", "PK": "Pakistan", "PH": "Philippines", "PN": "Pitcairn", "TM": "Turkmenistan", "PL": "Poland", "PM": "Saint Pierre and Miquelon", "ZM": "Zambia", "EH": "Western Sahara", "RU": "Russian Federation", "EE": "Estonia", "EG": "Egypt", "TK": "Tokelau", "ZA": "South Africa", "EC": "Ecuador", "IT": "Italy", "VN": "Vietnam", "SB": "Solomon Islands", "EU": "Europe", "ET": "Ethiopia", "SO": "Somalia", "ZW": "Zimbabwe", "SA": "Saudi Arabia", "ES": "Spain", "ER": "Eritrea", "ME": "Montenegro", "MD": "Moldova, Republic of", "MG": "Madagascar", "MF": "Saint Martin", "MA": "Morocco", "MC": "Monaco", "UZ": "Uzbekistan", "MM": "Myanmar", "ML": "Mali", "MO": "Macao", "MN": "Mongolia", "MH": "Marshall Islands", "MK": "Macedonia", "MU": "Mauritius", "MT": "Malta", "MW": "Malawi", "MV": "Maldives", "MQ": "Martinique", "MP": "Northern Mariana Islands", "MS": "Montserrat", "MR": "Mauritania", "IM": "Isle of Man", "UG": "Uganda", "TZ": "Tanzania, United Republic of", "MY": "Malaysia", "MX": "Mexico", "IL": "Israel", "FR": "France", "IO": "British Indian Ocean Territory", "FX": "France, Metropolitan", "SH": "Saint Helena", "FI": "Finland", "FJ": "Fiji", "FK": "Falkland Islands (Malvinas)", "FM": "Micronesia, Federated States of", "FO": "Faroe Islands", "NI": "Nicaragua", "NL": "Netherlands", "NO": "Norway", "NA": "Namibia", "VU": "Vanuatu", "NC": "New Caledonia", "NE": "Niger", "NF": "Norfolk Island", "NG": "Nigeria", "NZ": "New Zealand", "NP": "Nepal", "NR": "Nauru", "NU": "Niue", "CK": "Cook Islands", "CI": "Cote d'Ivoire", "CH": "Switzerland", "CO": "Colombia", "CN": "China", "CM": "Cameroon", "CL": "Chile", "CC": "Cocos (Keeling) Islands", "CA": "Canada", "CG": "Congo", "CF": "Central African Republic", "CD": "Congo, The Democratic Republic of the", "CZ": "Czech Republic", "CY": "Cyprus", "CX": "Christmas Island", "CR": "Costa Rica", "CV": "Cape Verde", "CU": "Cuba", "SZ": "Swaziland", "SY": "Syrian Arab Republic", "KG": "Kyrgyzstan", "KE": "Kenya", "SR": "Suriname", "KI": "Kiribati", "KH": "Cambodia", "KN": "Saint Kitts and Nevis", "KM": "Comoros", "ST": "Sao Tome and Principe", "SK": "Slovakia", "KR": "Korea, Republic of", "SI": "Slovenia", "KP": "Korea, Democratic People's Republic of", "KW": "Kuwait", "SN": "Senegal", "SM": "San Marino", "SL": "Sierra Leone", "SC": "Seychelles", "KZ": "Kazakhstan", "KY": "Cayman Islands", "SG": "Singapore", "SE": "Sweden", "SD": "Sudan", "DO": "Dominican Republic", "DM": "Dominica", "DJ": "Djibouti", "DK": "Denmark", "VG": "Virgin Islands, British", "DE": "Germany", "YE": "Yemen", "DZ": "Algeria", "US": "United States", "UY": "Uruguay", "YT": "Mayotte", "UM": "United States Minor Outlying Islands", "LB": "Lebanon", "LC": "Saint Lucia", "LA": "Lao People's Democratic Republic", "TV": "Tuvalu", "TW": "Taiwan", "TT": "Trinidad and Tobago", "TR": "Turkey", "LK": "Sri Lanka", "LI": "Liechtenstein", "A1": "Anonymous Proxy", "TO": "Tonga", "LT": "Lithuania", "A2": "Satellite Provider", "LR": "Liberia", "LS": "Lesotho", "TH": "Thailand", "TF": "French Southern Territories", "TG": "Togo", "TD": "Chad", "TC": "Turks and Caicos Islands", "LY": "Libyan Arab Jamahiriya", "VA": "Holy See (Vatican City State)", "VC": "Saint Vincent and the Grenadines", "AE": "United Arab Emirates", "AD": "Andorra", "AG": "Antigua and Barbuda", "AF": "Afghanistan", "AI": "Anguilla", "VI": "Virgin Islands, U.S.", "IS": "Iceland", "IR": "Iran, Islamic Republic of", "AM": "Armenia", "AL": "Albania", "AO": "Angola", "AN": "Netherlands Antilles", "AQ": "Antarctica", "AP": "Asia/Pacific Region", "AS": "American Samoa", "AR": "Argentina", "AU": "Australia", "AT": "Austria", "AW": "Aruba", "IN": "India", "AX": "Aland Islands", "AZ": "Azerbaijan", "IE": "Ireland", "ID": "Indonesia", "UA": "Ukraine", "QA": "Qatar", "MZ": "Mozambique"}, function(k, v) {
        countries.push({id: k, text: v});
    }); 
    $('.editable-country').editable({
        source: countries 
    }); 
	
	// List of All Currency
	
	var currency = [];
  $.each({ "Andorran Peseta": "ADP", "United Arab Emirates Dirham" : "AED",
  'Afghanistan Afghani': 'AFA',
'Albanian Lek': 'ALL',
 'Netherlands Antillian Guilder': 'ANG',
'Angolan Kwanza': 'AOK',
'Argentine Peso': 'ARS',
'Australian Dollar': 'AUD',
'Aruban Florin': 'AWG',
'Barbados Dollar': 'BBD',
'Bangladeshi Taka': 'BDT',
'Bulgarian Lev': 'BGN',
'Bahraini Dinar': 'BHD',
'Burundi Franc': 'BIF',
'Bermudian Dollar': 'BMD',
'Brunei Dollar': 'BND',
'Bolivian Boliviano': 'BOB',
'Brazilian Real': 'BRL',
'Bahamian Dollar': 'BSD',
'Bhutan Ngultrum': 'BTN',
'Burma Kyat': 'BUK',
'Botswanian Pula': 'BWP',
'Belize Dollar': 'BZD',
'Canadian Dollar': 'CAD',
'Swiss Franc': 'CHF',
'Chilean Unidades de Fomento': 'CLF',
'Chilean Peso': 'CLP',
 'Yuan (Chinese) Renminbi': 'CNY',
'Colombian Peso': 'COP',
 'Costa Rican Colon': 'CRC',
'Czech Republic Koruna': 'CZK',
'Cuban Peso' : 'CUP' ,
 'Cape Verde Escudo': 'CVE',
 'Cyprus Pound': 'CYP',
 'Danish Krone': 'DKK',
'Dominican Peso': 'DOP',
 'Algerian Dinar': 'DZD',
'Ecuador Sucre': 'ECS',
'Egyptian Pound': 'EGP',
 'Estonian Kroon (EEK)': 'EEK',
'Ethiopian Birr': 'ETB',
'Euro': 'EUR',
 'Fiji Dollar': 'FJD',
 'Falkland Islands Pound': 'FKP',
 'British Pound': 'GBP',
 'Ghanaian Cedi': 'GHC',
 'Gibraltar Pound': 'GIP',
 'Gambian Dalasi': 'GMD',
'Guinea Franc': 'GNF',
 'Guatemalan Quetzal': 'GTQ',
 'Guinea-Bissau Peso': 'GWP',
 'Guyanan Dollar': 'GYD',
 'Hong Kong Dollar': 'HKD',
 'Honduran Lempira': 'HNL',
'Haitian Gourde': 'HTG',
 'Hungarian Forint': 'HUF',
'Indonesian Rupiah': 'IDR',
 'Irish Punt': 'IEP',
 'Israeli Shekel': 'ILS',
'Indian Rupee': 'INR',
'Iraqi Dinar': 'IQD',
 'Iranian Rial': 'IRR',
'Jamaican Dollar': 'JMD',
'Jordanian Dinar': 'JOD',
'Japanese Yen': 'JPY',
'Kenyan Schilling': 'KES',
'Kampuchean (Cambodian) Riel': 'KHR',
'Comoros Franc': 'KMF',
 'North Korean Won': 'KPW',
'(South) Korean Won': 'KRW',
'Kuwaiti Dinar': 'KWD',
'Cayman Islands Dollar': 'KYD',
 'Lao Kip': 'LAK',
 'Lebanese Pound': 'LBP',
 'Sri Lanka Rupee': 'LKR',
'Liberian Dollar': 'LRD',
'Lesotho Loti': 'LSL',
 'Libyan Dinar': 'LYD',
 'Moroccan Dirham': 'MAD',
 'Malagasy Franc': 'MGF',
'Mongolian Tugrik': 'MNT',
'Macau Pataca': 'MOP',
'Mauritanian Ouguiya': 'MRO',

'New Yugoslavia Dinar': 'YUD',
'South African Rand': 'ZAR',
'Zambian Kwacha': 'ZMK',
'Zaire Zaire': 'ZRZ',
'Zimbabwe Dollar': 'ZWD',
'Slovak Koruna': 'SKK',
 'Armenian Dram': 'AMD',
 'Maltese Lira' : 'MTL',
'Mauritius Rupee' : 'MUR',
'Maldive Rufiyaa' : 'MVR',
'Malawi Kwacha' : 'MWK',
'Mexican Peso' : 'MXP',
'Malaysian Ringgit' : 'MYR',
'Mozambique Metical' : 'MZM',
'Namibian Dollar' : 'NAD',
'Nigerian Naira': 'NGN',
'Nicaraguan Cordoba' : 'NIO',
'Norwegian Kroner' : 'NOK',
'Nepalese Rupee' : 'NPR',
'New Zealand Dollar' : 'NZD',
'Omani Rial' : 'OMR',
'Panamanian Balboa' : 'PAB',
'Peruvian Nuevo Sol' : 'PEN',
'Papua New Guinea Kina' : 'PGK',
'Philippine Peso': 'PHP',
'Pakistan Rupee' : 'PKR',
'Polish Zloty' : 'PLN',
'Paraguay Guarani' : 'PYG',
'Qatari Rial' : 'QAR',
'Romanian Leu' : 'RON',
'Rwanda Franc' : 'RWF',
'Saudi Arabian Riyal' : 'SAR',
'Solomon Islands Dollar' : 'SBD',
'Seychelles Rupee' : 'SCR',
'Sudanese Pound' : 'SDP',
'Swedish Krona' : 'SEK' ,
'Singapore Dollar': 'SGD',
'St. Helena Pound': 'SHP',
'Sierra Leone Leone': 'SLL',
'Somali Schilling': 'SOS',
'Suriname Guilder': 'SRG',
'Sao Tome and Principe Dobra': 'STD',
'Russian Ruble': 'RUB',
'El Salvador Colon': 'SVC',
'Syrian Potmd': 'SYP',
'Swaziland Lilangeni': 'SZL',
'Thai Baht': 'THB',
'Tunisian Dinar': 'TND',
 'Tongan Paanga': 'TOP',
'East Timor Escudo': 'TPE',
 'Turkish Lira': 'TRY',
'Trinidad and Tobago Dollar': 'TTD',
 'Taiwan Dollar': 'TWD',
'Tanzanian Schilling': 'TZS',
'Uganda Shilling': 'UGX',
 'US Dollar': 'USD',
'Uruguayan Peso': 'UYU',
'Venezualan Bolivar': 'VEF',
'Vietnamese Dong': 'VND',
 'Vanuatu Vatu': 'VUV',
'Samoan Tala': 'WST',
'Silver Ounces': 'XAG',
'Gold Ounces': 'XAU',
'East Caribbean Dollar': 'XCD',
 'International Monetary Fund (IMF) Special Drawing Rights': 'XDR',
'CommunautÃ© FinanciÃ¨re Africaine BCEAO - Francs': 'XOF',
'Palladium Ounces': 'XPD',
'Comptoirs FranÃ§ais du Pacifique Francs': 'XPF',
'Platinum, Ounces': 'XPT',
'Democratic Yemeni Dinar': 'YDD',
'Yemeni Rial': 'YER',
 }, function(k, v) {
        currency.push({id: v, text: k});
    }); 
    $('#currency').editable({
        source: currency 
    });  
	
	//select industry 
				var industories = [];
  				$.each({ "Agriculture" : "Agriculture",
						 "Accounting" : "Accounting", "Advertising " : "Advertising ", "Aerospace " : "Aerospace ", "Aircraft " : "Aircraft",
						  "Airline" : "Airline", "Automotive " : "Automotive ",
						  "Banking " : "Banking ", "Broadcasting" : "Broadcasting", "Biotechnology" : "Biotechnology", 
						  "Call Centers " : "Call Centers ", "Cargo Handling " : "Cargo Handling ", "Chemical " : "Chemical ",
						   "Computer" : "Computer", "Consulting" : "Consulting", "Cosmetics" : "Cosmetics", "Defense" : "Defense",
						    "Department Stores " : "Department Stores ", "Education " : "Education ", "Electronics" : "Electronics", 
							"Entertainment & Leisure" : "Entertainment & Leisure", "Financial Services " : "Financial Services ",
							 "Food, Beverage & Tobacco " : "Food, Beverage & Tobacco ", "Transportation" : "Transportation",
							  "Telecommunications " : "Telecommunications ", "Sports" : "Sports", "Software " : "Software ",
							   "Soap & Detergent" : "Soap & Detergent", "Service" : "Service", 
							   " 	Securities & Commodity Exchanges " : " 	Securities & Commodity Exchanges ",
							    "Retail & Wholesale" : "Retail & Wholesale", "Private Equity" : "Private Equity",
								 "Online Auctions" : "Online Auctions",  "Newspaper Publishers" : "Newspaper Publishers",
								   " 	Music" : " 	Music",  " 	Motion Picture & Video" : " 	Motion Picture & Video", 
								    " 	Legal " : " 	Legal ",  " 	Investment Banking" : " 	Investment Banking", 
									 "Internet Publishing" : "Internet Publishing",   "Health Care " : "Health Care ",  
									 "Grocery " : "Grocery ",  "Other" : "Other",
				 }, function(k, v) {
        industories .push({id: v, text: k});
    }); 
    $('#industry').editable({
        source: industories  
    });  
	   
 
$('.editable').editable(); 
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

$("abbr.timeago").timeago();



});
</script>

 
</body>

</html>