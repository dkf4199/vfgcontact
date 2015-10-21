<!DOCTYPE html>
<html>

<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>CasinoTours.com - Let the fun begin!</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/casinotours.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/formsformatting.css" />
<script src="./js/jquery-1.9.1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script>
$(document).ready(function() {

	//Company dropdown list
	$('#rfid').change(function() {
		var rfid = "";
		rfid = $(this).val();
		
		rfidName = $("#rfid option:selected").html();
		if (rfid !== ''){
			//alert('RFID value: ' + rfidName);
			$.ajax({
				url:"ajaxgetcardholder.php",
				type: "POST",
				data: {rfid: rfid},
				dataType: "html",		
				success:function(result){
					$("#ajax_cardholder_info").html(result);
			}}); //end success:function
		}
	});
	
	
});	//end ready
</script>
<script>
function updateCardHolderInfo(){

	var firstName = $("#first_name").val();
	var lastName = $("#last_name").val();
	var email = $("#email").val();
		
	//Serialize the form
	var formdata = $('#location1_form').serialize();
	
	//alert(formdata);
	//call ajax_updatelead.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_insert_cardholder.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_insert_feedback").html(result);
		}	//end success:function
	}); //end $.ajax 
	
	/*alert("Location Form Button clicked.\n"+
		  "First Name: "+firstName+"\n"+
		  "Last Name: "+lastName+"\n"+
		  "Email: "+email);
	//alert("Update Form Button clicked.\n"+"Data Changed Flag: "+dataChanged); */
	return false;
}
</script>
</head>

<body>
<div class="wrapper  showgoldborder">
  <div class="header">
	<div class="headerdivleft">
		<span>Welcome to Location 1</span>
	</div>
	<div class="headerdivright">
		<span>CasinoTours.com - Let the Fun Begin.</span>
	</div>
  </div>
  <div class="cleardiv"></div>
  
	<div class="maincontent">
			<label for="rfid">Card Swipe Sim:</label>
			<?php
				include ('includes/connect_parms.php');
				try {
					$dbh = new PDO(MYSQL_PDO_HOSTSTRING, DB_USER, DB_PASSWORD);
					//ERROR MODE LEVEL
					//$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );  
					//$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );  
					$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					
					
					$sth = $dbh->prepare("SELECT rfid FROM cards");
					$sth->execute();
					
					$result = $sth->fetchAll();
					
					# If one or more rows were returned...
					if ( count($result) > 0 ) {
						echo '<select name="rfid" id="rfid"><option value="">Swipe Card</option>';
						foreach($result as $row) {
							// list the rfid's for now
							if ($rfid == $row['rfid']){
								$sel = 'selected="selected"';
							}
							else{
								$sel = '';
							} 
							echo '<option '.$sel.' value="'.$row['rfid'].'">'.$row['rfid'].'</option>';
						}
						echo '</select>';
					} else {
						echo '<p>No rfid\'s retrieved.</p>';
					}
					
					//check to see if this rfid is in card_holders table yet
					$sth = $dbh->prepare("SELECT rfid, firstname, lastname, email, start_date 
										  FROM card_holders");
					$sth->execute();
					
					$result = $sth->fetchAll();
					
					# If one or more rows were returned...
					//finish the card swipe simulation form
					//echo '<input type="hidden" name="swipesubmit" value="swipe" />
					//	<input type="submit" class="swipebutton" value="Swipe Card" />
					//	</form>';
										
					//close handle
					$dbh = null;
					
				} catch (PDOException $e) {
					/*print "PDO Error!:<br /> " . $e->getMessage() . "<br/>";
					die();
					*/
					
					//OR USE THIS BLOCK
					echo "CasinoTours System Error.  Check appropriate error log";
					$today = date("Y-m-d H:i:s");
					$err = "CasinoTours.com  ".$today." ==> ".$e->getMessage()."\n";
					file_put_contents('PDOErrors.txt', $err, FILE_APPEND);
				}
			?>
		<div id="ajax_cardholder_info">Select a card above.</div>
		
	</div> <!-- close main content -->
	
	<div class="footer">
		<ul>
			<li><a href="#">Link 1</a></li>
			<li><a href="#">Link 2</a></li>
			<li><a href="#">Link 3</a></li>
		</ul>
	</div> <!-- close footer -->

</div>	<!-- close wrapper -->
</body>
</html>