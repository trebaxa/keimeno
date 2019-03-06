<html>
<head>
<link rel="stylesheet" href="<% $PATH_CMS %>js/plugins/webcam/layout.css" type="text/css">
<title><% $webcamobj.FORM_CON.title %></title>
<script  type="text/javascript" src="<% $PATH_CMS %>cjs/cjs/ajax_class.js"></script>
</head>
<body onmouseover="return true" onkeypress="return true" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">

<br><table  width="100%" cellpadding="3">
<tr>
<td colspan="4" align="center">
<% if ($customer.kid > 0) %>
 Your comment: <input style="font-size: 8pt; background-color: #000000; color: #FFFFFF; border: 1pt solid white; border-collapse: collapse;" type="text" size="60" onKeyDown="checkKeyPress(event, 'btn_submit')" ID="eintrag" name="eintrag" value="">
 <input class="submit" ONCLICK="sendRequest2InnerHTML('eintrag','myspan','a_save','shout','&event_id=<% $SHOUT.event.EID %>',1)" type="BUTTON" id="btn_submit" name="btn_submit" value="Eintragen">
<% else %>
 Please register for chatting. / Du must registriert sein, um zu chatten.
<%/if %>

</td></tr>
<tr><td colspan="4" align="center">
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :baby:')"><img src="./smilies/baby.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :D')"><img src="./smilies/biggrin.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' ?(')"><img src="./smilies/confused.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' 8)')"><img src="./smilies/cool.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' ;(')"><img src="./smilies/crying.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' 8o')"><img src="./smilies/eek.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :evil:')"><img src="./smilies/evil.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :(')"><img src="./smilies/frown.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :))')"><img src="./smilies/happy.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' X(')"><img src="./smilies/mad.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :]')"><img src="./smilies/pleased.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :O')"><img src="./smilies/redface.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :rolleyes:')"><img src="./smilies/rolleyes.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :)')"><img src="./smilies/smile.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :P')"><img src="./smilies/tongue.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' :tongue:')"><img src="./smilies/tongue2.gif" ></a>
<a href='JavaScript:void(0)' onClick="addValueOverID('eintrag',' ;)')"><img src="./smilies/wink.gif" ></a>
</td>
</tr></table>
<script  type="text/javascript">
<!--
function setSecs() {
GetRequest2InnerHTML('myspan','a_get','shout','&event_id=<% $SHOUT.event.EID %>');
setTimeout("setSecs()", <% $SHOUT.chat_restart %>);
}
setTimeout("setSecs()",<% $SHOUT.chat_restart %>);
//-->
</script>
<span name="myspan" id="myspan">
<% include file="webcam_shoutbox_table.tpl" %>
</span>
</body></html>
