//var xmlHttp;
function callAjax(phone) {
	$.ajax({
			url:"ajaxgettemp_contact.php",
			type: "POST",
			data: {phone: phone},
			dataType: "html",		
			success:function(result){
				//$("#dialermessages").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				//$("#dialermessages").html(errorThrown);
			}
	}); //end $.ajax

	/*if (phone.length == 0) { 
  	     return document.getElementById("contacts").innerHTML="";
        } 
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null) {
  	    return alert ("Browser does not support HTTP Request");
        } 
	var url="ajaxgettemp_contact.php"; 
	url=url+"?phone="+phone; 
	
	xmlHttp.onreadystatechange=stateChanged; 
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
	*/
}

function stateChanged(){ 
	if (xmlHttp.readyState==4)
 	{ //alert(xmlHttp.responseText);
 		//document.getElementById("contacts").innerHTML=xmlHttp.responseText;  //Don't think this is needed
		
 	} 
}

function GetXmlHttpObject() {
	var xmlHttp=null;
	try {
 		// Firefox, Opera 8.0+, Safari
 		xmlHttp=new XMLHttpRequest();
 	}
	catch (e) {
 		// Internet Explorer
 		try {
  		    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  		}
 		catch (e) {
  		    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  		}
 	}
	return xmlHttp;
}
