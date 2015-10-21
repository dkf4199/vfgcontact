$(document).ready(function() {

	$( "#accordion" ).accordion();

	// REP - General Summary Report
	//********************************************************
	$('#reports_list a.gen_rep_summary').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/general_rep_summary.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//****** END general rep summary report *************************
	
	// REP - Purchased Policy
	//***************************************************************
	$('#reports_list a.rep_purchased_policy').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/rep_policy_purchased.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//****** END rep purchased policy *******************************
	
	// PROSPECT - Tier 2 Calls Scheduled
	//***************************************************************
	$('#reports_list a.tier2_calls_scheduled').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier2_call_scheduled.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	// PROSPECT - Tier 2 Calls Made
	//***************************************************************
	$('#reports_list a.tier2_calls_made').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier2_call_made.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	// PROSPECT - Tier 3 Calls Scheduled
	//***************************************************************
	$('#reports_list a.tier3_calls_scheduled').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier3_call_scheduled.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	// PROSPECT - Tier 3 Calls Made
	//***************************************************************
	$('#reports_list a.tier3_calls_made').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier3_call_made.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	// PROSPECT - Tier 4 Calls Scheduled Report
	//***************************************************************
	$('#reports_list a.tier4_calls_scheduled').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier4_call_scheduled.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	// PROSPECT - Tier 4 Calls Made
	//***************************************************************
	$('#reports_list a.tier4_calls_made').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tier4_call_made.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//****************************************************************
	
	// PROSPECT - Tier Calls Scheduled - BY REP
	//***************************************************************
	$('#reports_list a.tier_calls_scheduled_by_rep').click(function (e) {
		e.preventDefault();
		
		//AJAX
		$.ajax({
			url:"./ajax_reports/tiercalls_scheduled_by_rep.php",
			type: "GET",
			data: {},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewreports").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	//***************************************************************
	
	
});		//END DOCUMENT READY
//*************************************************************************//
function filterReport(tierstatus){

	switch (tierstatus) {
		case '2A':
			var assigned_manager = $("#assigned_manager_filter :selected").val();
			//alert(assigned_manager);
			//AJAX
			$.ajax({
				url:"./ajax_reports/tier2_call_scheduled_filters.php",
				type: "GET",
				data: {assigned_manager: assigned_manager},
				dataType: "html",		
				success:function(result){
					$("#ajax_viewreports").html(result);
				}	//end success:function
			}); //end $.ajax
			break;
		case '3A':
			break;
		case '4A':
			break;
		case 'TierCallsScheduledByAgent':
			var assigned_agent = $("#assigned_agent_filter :selected").val();
			//alert(assigned_agent);
			//alert(assigned_manager);
			//AJAX
			$.ajax({
				url:"./ajax_reports/tiercalls_scheduled_by_rep_filters.php",
				type: "GET",
				data: {assigned_agent: assigned_agent},
				dataType: "html",		
				success:function(result){
					$("#ajax_viewreports").html(result);
				}	//end success:function
			}); //end $.ajax
			break;
	}
	
	return false;
}