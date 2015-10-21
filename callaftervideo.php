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
		<b>Voice Mail</b><br />

		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group.

		We had a call scheduled at this time today. Please give me a call at '.$repphone.'  

		so that we can discuss the video that we spoke about.

		Once again that\'s '.$repfirst.' '.$replast.' with VFG at '.$repphone.'.

		I look forward to hearing from you soon.<br />

		Have a great day.<br /><br />
		<a href="#" class="savevm" onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'aftervideo\',\'vm\');">Left VM. Save to History.</a>&nbsp;
		<input type="checkbox" name="cav_vm_trifecta_email" id="cav_vm_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="cav_vm_trifecta_text" id="cav_vm_trifecta_text" value="Y" >Send Text&nbsp;
		</p>

		<p>
		<b>Conversation</b><br />

		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group.

		Did you get a chance to watch the video that we spoke of?<br /><br />

		<b>NO.</b> Well, when would be a good time for us to talk after you have had a chance to watch it?<br />

		<i>(Deal with the response accordingly.)</i><br /><br />

		<b>YES, I DID.</b> Great, what did you like most about it?<br />

		<i>(Deal with their response and agree that that is a very special part about what we are doing.)</i><br />

		<i>(Discuss a few more points about the video.)</i><br /><br />

		The next step in the process is for you to speak with a manager who will show you in more detail how 

		are systems work and the great tools that the company makes available to you for you to succeed.<br />

		You will need about 30-45 minutes to go through everything and you will need to be in front of a 

		computer with internet service. When would be a good time for the call?<br /><br />

		<i>(Deal with the response accordingly and go to the Google calendar and set up the time with the manager 

		that you are working with.)</i><br />

		<i>(Let them know if you will be joining the call or not. Also explain to them that you have just completed 

		the responsibilities required for a tier 1.)</i><br /><br />

		Excuse yourself from the conversation accordingly.<br /><br />
		<a href="#" class="saveconvo" onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'aftervideo\',\'cv\');">Had Conversation. Save to History.</a>&nbsp;
		<input type="checkbox" name="cav_cv_trifecta_email" id="cav_cv_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="cav_cv_trifecta_text" id="cav_cv_trifecta_text" value="Y" >Send Text&nbsp;
		</p>';

	echo $scriptstr;
?>