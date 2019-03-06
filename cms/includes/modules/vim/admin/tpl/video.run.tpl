<% if ($cmd=="query_run") %>
<html>
<head>
<link rel="stylesheet" type="text/css" href="layout.css">
</head>
<body>
<div style="width:100%;background:#ffffff;">
<div style="width:400px">
<fieldset>	
<legend>Status</legend>
<table>
<tr><td>Total:</td><td><% $VIM.sync_status.TotalResults %> (max. <% $VIM.query.qobj.maxTotalLimit %>)</td></tr>
<tr><td>Verglichen:</td><td><% $VIM.sync_status.FORM.YTOPTIONS.startIndex %></td></tr>
<tr><td>Verglichen (%):</td><td><div class="processbarcon"><div class="processbar" style="width:<% $VIM.sync_status.YTOPTIONS.doneProcent %>%;"><% $VIM.sync_status.YTOPTIONS.doneProcent %>%</div></div></td></tr>
<!--
<tr><td>Added:</td><td><% $VIM.sync_status.vp_log.count_added %></td><td>Skipped:</td><td><% $VIM.sync_status.vp_log.count_skipped %></td></tr>
-->
</table>
</fieldset>	
</div></div>
</body></html>
<%/if%>


