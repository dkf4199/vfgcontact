<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	$h_link = '';
	if (isset($_SESSION['homepage_link'])){
		$h_link = $_SESSION['homepage_link'];
	}
	
	$cid = $_GET['contact_id'];
	$fname = $_GET['contact_firstname'];
	$lname = $_GET['contact_lastname'];
	$repfirst = $_GET['reps_firstname'];
	$replast = $_GET['reps_lastname'];
	$repphone = $_GET['reps_phone'];
	
	$scriptstr = '<p>
		<b>Voice Mail</b> (Your energy and tone of voice matter)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Leave voice mail only on the 3rd attempt.</b><br /><br />
		
		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group calling back again.  Please give me a call at '.$repphone.' so we can discuss the videos.
		Have you been able to watch it yet?  You had shown interest in our virtual financial services business model you can do from home. My website is '.$h_link.'.  Once again,
		that\'s '.$repfirst.' '.$replast.' with VFG at '.$repphone.'. Please give me a call. I look forward to hearing from you soon.<br />
		Have a great day.<br /><br />
		<a href="#" class="savevm" onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'intro2\',\'vm\');">Save Voicemail - Send Email/Text</a>&nbsp;
		<input type="checkbox" name="ic2_vm_trifecta_email" id="ic2_vm_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="ic2_vm_trifecta_text" id="ic2_vm_trifecta_text" value="Y" >Send Text<br /><br />
		</p>
		
		<p>
		<b>Conversation</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="#" class="saveconvo" 
			onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'intro2\',\'cv\');">Save Conversation to History.</a>&nbsp;&nbsp;&nbsp;
		<a href="#" class="saveconvo" 
			onClick="javascript: return goToTierCallScheduler(\''.$cid.'\');">Schedule Tier 2 Meeting.</a>&nbsp;&nbsp;&nbsp;
		<br /><br />
		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group. Did you get a chance to watch the video on my website?<br /><br />
		<b>NO.</b> Well, you need to watch the videos, when would be a good time for you to watch it?  <i>(Get a firm committment for a specific time)</i><br>
		OK, great.  Once you watch it we have a live webinar tomorrow at ________ or __________ that overviews the virtual office & 
		our technology to see if we are a fit.  Which one would be better for you?<br /><br />
		<b>YES I DID.</b> Great,wasn\'t that powerful, what did you like most about it?<br /><br />
		<i>(listen closley to their response and take notes: DO NOT Interrupt them Deal with their response and agree that that is a very special part about what we are doing.)</i><br />
		<i>(We have great resources & tools, no meetings, no appts, larger market, delegate sales, great comp, living benefits and much more....)</i><br /><br />
		The next step in the process is for you to watch a live webinar that overviews the virtual office & our technology to see if we are a fit.<br />
		We have one tomorrow at _________ or _________.  Which one would be better for you?<br /><br />
		<i>Go to the VFGpro "VFG Calendar" menu and YOU register them for the appropriate webinar.</i><br />
		<i>Then in the VFG Tier Manager or VFGcontact.com set up a reminder for them to get an email and text reminder for the Tier 2 webinar.</i><br />
		<i>Then drop down and click "Call Scheduled".</i><br />
		<i>A calendar will appear and you click on the Tier 2 date then put the time in their time zone and 00 minutes.</i><br />
		<i>Put the notification interval at 30 min and make sure sure it says "yes" on email and "yes" on text to remind them.</i><br />
		<i>Then put the notification interval at 30 min and make sure sure it says "yes" on email and "yes" on text to remind them.</i><br />
		<i>Click "Add/Update"</i><br />
		<i>Let them know you will be calling them (Or they can call you) right after the webinar to answer any questions and help them get started if they are ready.</i><br />
		</p>';

	echo $scriptstr;
?>