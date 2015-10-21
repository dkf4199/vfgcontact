<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
		
	$cid = $_GET['contact_id'];
	$fname = $_GET['contact_firstname'];
	$lname = $_GET['contact_lastname'];
	$repfirst = $_GET['reps_firstname'];
	$replast = $_GET['reps_lastname'];
	$repphone = $_GET['reps_phone'];
	
	$scriptstr = '<p>
		<b>Voice Mail</b> (Your energy and tone of voice matter)&nbsp;&nbsp;&nbsp;
		<a href="#" class="savevm" onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'intro\',\'vm\');">Left VM. Save to History.</a>&nbsp;
		<input type="checkbox" name="ic_vm_trifecta_email" id="ic_vm_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="ic_vm_trifecta_text" id="ic_vm_trifecta_text" value="Y" >Send Text</br /><br />
		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group.
		I am calling because you had recently inquired about our virtual business model in the financial services 
		industry.<br />
		We have an incredible story and powerful income opportunity and I would like to see if you had a chance yet<br>
		to view both of our short videos and speak with you about it. So, please give me a call 
		at '.$repphone.'.<br />
		Once again that\'s '.$repfirst.' '.$replast.' with the Virtual Financial Group at '.$repphone.'. I 
		look forward to hearing from you soon.<br />
		Have a great day.
		</p>

		<p>
		<b>Conversation</b>&nbsp;&nbsp;&nbsp;
		<a href="#" class="saveconvo" 
			onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'intro\',\'cv\');">Had Conversation. Save to History.</a>&nbsp;
		<input type="checkbox" name="ic_cv_trifecta_email" id="ic_cv_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="ic_cv_trifecta_text" id="ic_cv_trifecta_text" value="Y" >Send Text&nbsp;<br /><br />

		Hello, is this '.$fname.'? This is '.$repfirst.' '.$replast.' with the Virtual Financial Group.
		I am calling because you had recently inquired about our virtual business model in the financial services 
		industry. We have an incredible story and powerful income opportunity and I would like to speak with you more about it.<br /><br />
		Have you had a chance to see the 20 minute video yet?<br /><br />
		<b>NO.</b> Ok.  No problem. We have a 20 minute video that gives the details on the ground-breaking virtual business system
		and revolutionary products that everyone in America needs<br /> and how you can be very successful by being the one who introduces
		them to it.  Would you like the link to the site or would you rather me text or email it to you?<br /><br />
		When do you think you will have time to watch the video?  Great.  Could we set up a time after that to spend a few minutes
		to see if this may be an opportunity that may work for you?<br /><br />
		<i>{Deal with the response and set an appointment to call.}</i><br /><br />
		<b>YES.</b> Great. It is pretty amazing what we are doing virtually in the financial services industry.  Over 54 billion dollars in
		commissions are paid out every year by insurance companies.<br />With our system you can make a great commission income without
		having to be an expert in sales or insurance. Our 20 minute video gives all the details on our ground-breaking<br />virtual business system and revolutionary products that
		everyone in America needs and how you can be very successful by being the one who introduces them to it.<br /><br />
		When would be a good time for me to call you back so we can walk you through the power of our virtual business system and products<br />
		online and answer any questions you may have?
		<i>(Set the time in the calendar for your financial manager and tell them that you will be in touch.)</i><br /><br />
		Have a great day.<br /><br />
		
		</p>';
	
	echo $scriptstr;
?>