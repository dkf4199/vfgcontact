<html>
<head>
<title></title>
<script>
function js_clock() {
    var clock_time    = new Date();
    var clock_hours   = clock_time.getUTCHours() - 6;
    var clock_minutes = clock_time.getUTCMinutes();
    var clock_seconds = clock_time.getUTCSeconds();
    var clock_suffix  = "AM";

    if (clock_hours < 0) {
      clock_hours += 24;
    }

    if (clock_hours > 11) {
      clock_suffix = "PM";
      clock_hours -= 12;
    }

    if (clock_hours == 0) {
      clock_hours = 12;
    }

    if (clock_hours < 10) {
      clock_hours = "0" + clock_hours;
    }

    if (clock_minutes < 10) {
      clock_minutes = "0" + clock_minutes;
    }

    if (clock_seconds < 10) {
      clock_seconds = "0" + clock_seconds;
    }

    var clock_div = document.getElementById('js_clock');

    clock_div.innerHTML = clock_hours + ":" + clock_minutes + " " + clock_suffix;
    setTimeout("js_clock()", 1000);
 }
 
function getClock()
{
var clock = new Date();
var hours12;
var ampm = "AM";
var hours24 = clock.getHours();
var minutes = clock.getMinutes();
var seconds = clock.getSeconds();
if (hours24 >= 13)
{
hours12 = hours24 - 12;
ampm = 'PM';
}
else if (hours24 == 12)
{
hours12 = 12;
ampm = 'PM';
}
else if (hours24 == 0)
{
hours12 = 12;
}
else
{
hours12 = hours24;
}
if(hours12 < 10)
{
hours12 = '0' + hours12;
}
if (minutes < 10)
{
minutes = '0' + minutes;
}
if(seconds < 10)
{
seconds =  '0' + seconds;
}
var time = hours12 + ' : ' + minutes + ' : ' + seconds + ' ' + ampm;
document.title = time;
document.getElementById('js_clock').innerHTML = time;
timer = setTimeout('getClock()',1000);
}

window.onload = function()
{
  getClock();
  //js_clock();
}
</script>
<!-- <body onload="getClock()">-->
<body>
<div id="js_clock"></div>
</body>
</html>