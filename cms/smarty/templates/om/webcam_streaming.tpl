<html>
<head>
<link rel="stylesheet" href="<% $PATH_CMS %>js/plugins/webcam/layout.css" type="text/css">
<title><% $webcamobj.FORM_CON.title %></title>
<script  type="text/javascript" src="<% $PATH_CMS %>cjs/cjs/ajax_class.js"></script>
<script  type="text/javascript" src="<% $PATH_CMS %>js/plugins/webcam/livestream.js"></script>
<script  type="text/javascript" src="<% $PATH_CMS %>js/plugins/webcam/s_functions.js"></script>
</head>
<body >
<!--
onmouseover="return true" onkeypress="return true" ondragstart="return false" onselectstart="return false" oncontextmenu="return false"
-->

<h1><% $webcamobj.FORM_CON.title %></h1>
<span ID="other_cams" name="other_cams"></span>

<% if ($customer.kid<=0) %>
<div class="infobox">Bitte loggen Sie sich ein, um diesen Live Stream zu sehen.</div>
<%/if%>

<% if ($customer.kid>0) %>
<div style="border:1px solid dashed #80161E; text-align:center; padding:6px;width:99%;">
   <table  width="99%"><tr><td valign="top">
<div align="center" name="astronation_live" id="astronation_live"></div>
<!--<div align="center"><a href="javascript:livestream_fullscreen('../streaming/streaming.php?aktion=a_fullscreen&streamid=<%$webcamobj.c_kid%>&event_id=<%$webcamobj.EID%>">Fullscreen</a><br>-->
    </td>
<td valign="top" align="center"><font color="#FFFFFF"><u>Wer schaut zu?</u>
<br><span id="whoiswatching" name="whoiswatching"></span></font></td></tr></table>
</div>

<% if ($webcamobj.c_dialog_frame==1) %>
<iframe src="<% $PATH_CMS %>streaming/stream_dialog.php?streamid=<%$webcamobj.c_kid%>&aktion=read&event_id=<%$webcamobj.EID%>" width="99%" height="161" name="stream_dialog_<%$webcamobj.EID%>" scrolling="yes" marginheight="0" marginwidth="0" frame target="_self" style="border: 1px solid #80161E;"></iframe>
<hr>
<%/if%>
<% if ($webcamobj.c_chat_frame==1) %>
<span class="header">Dialog to sender:</span><br>
<iframe name="window_shout_<%$webcamobj.EID%>" src="<% $PATH_CMS %>streaming/shout.php?restart=30&event_id=<%$webcamobj.EID%>" width="99%" height="50%" scrolling="yes" marginheight="0" marginwidth="0" frame target="_self" style="border: 1px solid #80161E;">
Please activate IFRAME in your Browser</iframe>
<%/if%>

<br>
<font color="#FFFFFF">Um alle Funktionen in diesem Fenster nutzen zu kÃ¶nnen, muss JavaScript und IFRAME in Ihrem Browser aktiviert sein.<br>To use this window with all its functions, you have to activate JavaScript and IFRAME in your browser.<br>This page is designed for InternetExplorer(R).</font>

<script  type="text/javascript">
<!--
 //live_obj.stream_pic="";
 live_obj.start(<%$webcamobj.c_kid%>,<%$webcamobj.EID%>,<%$webcamobj.c_cam_speed%>);

 function setRefreshTimer() {
 refreshStreamingFrame(<%$webcamobj.EID%>, <%$webcamobj.c_kid%>);
 setTimeout("setRefreshTimer()", 180000); //3min
}
setRefreshTimer();

function reloadCompletePage() {
var a= Math.floor(Math.random()*10000000);
 document.location.href="<% $PATH_CMS %>streaming/streaming.php?streamid=<%$webcamobj.c_kid%>&event_id=<%$webcamobj.EID%>&rnd="+a;
}
// setTimeout("reloadCompletePage()", 180000);

//-->
</script>

<%/if%>

</body>
</html>
