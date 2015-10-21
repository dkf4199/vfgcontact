<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery-1.9.1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script>
var tier1 = ["ANo Initial Contact",
			"B3x3 in Progress",
			"CContacted 25 min video sent",
			"DFollow-up after video",
			"ESchedule Tier 2 call"];

var tier2 = ["ACall Scheduled",
			"BCall Made",
			"CContact Signed Up, Schedule Tier 3 Call",
			"DTier 3 Call"];
			
var tier3 = ["ACall Scheduled",
			"BCall Completed",
			"CFast Track",
			"DTraditional",
			"ESchedule Tier 4 Call"];
			
var tier4 = ["ACall Scheduled",
			"BCall Made",
			"CPolicy Yes",
			"DPolicy No"];

						 
function setList(val){
	//alert(val);
	var optionstring = ""
	switch(val) {
		case '1':
		  for (var i in tier1){
			optionstring += "<option value=\"" + tier1[i].substr(0,1) + "\">" + tier1[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '2':
		  for (var i in tier2){
			optionstring += "<option value=\"" + tier2[i].substr(0,1) + "\">" + tier2[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '3':
		  for (var i in tier3){
			optionstring += "<option value=\"" + tier3[i].substr(0,1) + "\">" + tier3[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '4':
		  for (var i in tier4){
			optionstring += "<option value=\"" + tier4[i].substr(0,1) + "\">" + tier4[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		default:
		  optionstring = "<option value=\"no list\">No List</option>" + "\n";
	}
	$("select[name='tierstep']").find('option').remove().end().append($(optionstring));
}
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<div class="header">
		<span>EAPCRM.com.  Leads Management - "It's easy as pie..."</span>
	</div>
	<div class="topnav">
		<ul id="menu_top">
			<li><a href="rep_maindash.php">Home</a></li>
			<li><a href="rep_add_contact.php">Add Contact</a></li>
			<li><a href="rep_manage_contacts.php">Manage Contacts</a></li>
			<li><a href="rep_logout.php">Logout</a></li>
		</ul>
	</div>
	
	<div class="maincontent">
	
	<div class="formdiv">
		<ul>
			<li>
				<label>Tier:</label>
				<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);" checked /><label for="tier1">1</label>
				<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);" /><label for="tier2">2</label>
				<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);" /><label for="tier3">3</label>
				<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);" /><label for="tier4">4</label>
			</li>
			<li>
				<label>TierStep:</label>
				<select name="tierstep" id="tierstep">
					<option value="A">Tier 1 step 1</option>
					<option value="A">Tier 1 step 2</option>
					<option value="A">Tier 1 step 3</option>
				</select>
				</span>
			</li>
		</ul>
		
	</div>					
	</div> <!-- close main content -->
	
</div>	<!-- close wrapper -->
</body>
</html>