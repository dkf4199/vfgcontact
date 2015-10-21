/*
Auto tabbing script- By JavaScriptKit.com
http://www.javascriptkit.com
This credit MUST stay intact for use
*/

function autotab(original,destination){
if (original.getAttribute&&original.value.length==original.getAttribute("maxlength"))
destination.focus()
}
