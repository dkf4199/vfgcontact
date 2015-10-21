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
<link rel="stylesheet" type="text/css" media="screen" href="./css/jqModal.css" />
<link href="./css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<!-- Load JavaScript files -->
<script type='text/javascript' src="./js/jquery.js"></script>
<script type='text/javascript' src="./js/jqModal.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>

<script>
$(document).ready(function() {
	$('#dialog').jqm({modal: true, trigger: 'a.showDialog'});
});
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<?php include ('./includes/html/header_nolog.html'); ?>
	
	<div class="maincontent">
		<a href="#" class="showDialog">view</a>

		<div class="jqmWindow" id="dialog">

			<a href="#" class="jqmClose">Close</a>
			<hr>
			<em>READ ME</em> -->
			This is a "vanilla plain" jqModal window. Behavior and appeareance extend far beyond this.
			The demonstrations on this page will show off a few possibilites. I recommend walking
			through each one to get an understanding of jqModal <em>before</em> using it.

			<br /><br />
			You can view the sourcecode of examples by clicking the Javascript, CSS, and HTML tabs.
			Be sure to checkout the <a href="README">documentation</a> too!

			<br /><br />
			<em>NOTE</em>; You can close windows by clicking the tinted background known as the "overlay".
			Clicking the overlay will have no effect if the "modal" parameter is passed, or if the
			overlay is disabled.
		</div>
		
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->

<!-- contact.js contains the jquery that runs the modal divs -->
<!--<script type='text/javascript' src='./js/contact.js'></script>-->
</body>
</html>