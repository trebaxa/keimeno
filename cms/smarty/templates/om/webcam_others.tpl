<b>Andere Live Feeds</b>
<div style="text-align:center;width:100%;">
<table style="border:solid #80161E 1px;">
 <% foreach from=$webcams_online item=wc %>
<tr>
<td class="<% $wc.class %>"><% $wc.C_TITLE %>
<br>
<a onClick="livestream_pop('<% $PATH_CMS %><% $webcamobj.campic.link %>');" href="#"><img src="<% $webcamobj.campic.thumb %>" ></a>
</td>
</tr>
<%/foreach%>
</table></div>
