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
<link rel="stylesheet" type="text/css" media="screen" href="./css/simplemodal_form.css" />
<link href="./css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />

<script>
/*$( document ).ready(function() {
    $("#modallink").click(function() {
		//alert( "You clicked a link!" );
		$("#openModal").modal();
	});
	
	
});*/
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<?php include ('./includes/html/header_nolog.html'); ?>
	
	<div class="maincontent">
		<!-- contact-form div - click events in jquery -->
		<div id='contact-form'>
		 <input type='button' name='contact' value='Demo' class='contact demo'/> or <a href='#' class='contact'>Demo</a>
		</div>
		<div id='contact-form'>
		 <input type='button' name='contact' value='Demo' class='contact demo'/> or <a href='#TC46024' class='contact'>Demo With cid</a>
		</div>
		<!-- preload the images -->
		<div style='display:none'>
			<img src='./images/loading.gif' alt='' />
		</div>
		
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
<!-- Load JavaScript files -->
<script type='text/javascript' src="./js/jquery.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- contact.js contains the jquery that runs the modal divs -->
<script type='text/javascript' src='./js/contact.js'></script>
</body>
</html>