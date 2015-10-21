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
<link rel="stylesheet" type="text/css" media="screen" href="./css/modal.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<script>
$(document).ready(function(){
        $("#modal-launcher").click(function(){
            $("#modal-background").toggleClass("active");
            $("#modal-content").toggleClass("active");
        });
        
        $("#modal-background, #modal-close").click(function(){
            $("#modal-background").toggleClass("active");
            $("#modal-content").toggleClass("active");
        });
    });
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
			<li><a href="#">Active Contacts</a>
				<ul id="static_rpt_submenu">
					<li><a href="rep_manage_contacts.php">Manage Contacts</a></li>
					<li><a href="rep_manage_tasks.php">Manage Tasks</a></li>
					<li><a href="rep_manage_team.php">Manage Team</a></li>
				</ul>
			</li>
			<li><a href="rep_dumped_contacts.php">Dumped Contacts</a></li>
			<li><a href="rep_logout.php">Logout</a></li>
		</ul>
	</div>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo 'User.'; ?></p>
		
		<h1>Bacon ipsum dolor sit amet</h1>

		<p>Magna adipisicing eu, pig ex pariatur non biltong nisi consequat do exercitation. Biltong exercitation consequat aute. Excepteur velit ribeye, et salami pariatur sed consequat enim ham. Tenderloin consequat et, in pastrami aute meatloaf beef spare ribs tri-tip beef ribs sed ut jerky strip steak. Fugiat turkey shank frankfurter pork loin pastrami.</p>

		<button id="modal-launcher">Launch Modal Window</button>

		<div id="modal-background"></div>
		<div id="modal-content">
			<button id="modal-close">Close Modal Window</button>
		</div>
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>