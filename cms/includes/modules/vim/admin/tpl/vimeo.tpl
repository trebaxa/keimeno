<div class="page-header"><h1>Videothek</h1></div>

<div class="btn-group">
<a class="btn btn-secondary" href="<%$PHPSELF%>?cmd=sync&epage=<%$epage%>">Sync. mit Vimeo</a>
<% if ($VIM.loggedin==FALSE) %>
  <a class="btn btn-secondary" href="<% $VIM.vi_authlink %>">authenticate Vimeo application</a>
<%/if%>
</div>

<% if ($section=='modstylefiles') %>
 <% include file="modstylefiles.tpl"%>
<%/if%>

<% if ($cmd==load_videos) %>
 <% if (count($VIM.video_list)>0) %>
	<table class="table table-striped table-hover" >
	<thead><tr>
		<th width="100"></th>
		<th width="200">Titel</th>
		<th width="100">L&auml;nge</th>
		<th width="300">Beschreibung</th>
		<th>Upload Datum</th>
		<th></th>
	</tr></thead>	
	<% foreach from=$VIM.video_list item=file %>
	<tr>
	 <td><img width="100" src="<% $file.v_thumbnailurl  %>" ></td>
	 <td><a target="_video" href="../index.php?page=9810&id=<% $file.v_videoid %>&cmd=load_video_fe"><% $file.v_videotitle %></a></td>
	 <td><% $file.v_duration %></td>
	 <td><% $file.v_videodescription|truncate:300  %></td>
	 <td><% $file.v_recorded_ger %></td>
	 <td> <% foreach from=$file.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
	</tr>
	<%/foreach%>
	</table> 
	 
	<%else%>
<div class="alert alert-info">No results.</div>
	<%/if%>	
<%/if%>


<% if ($cmd=="sync") %>
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
<tr><td>Total:</td><td><% $VIM.sync_status.TotalResults %></td></tr>
<tr><td>Verglichen:</td><td><% $VIM.sync_status.FORM.YTOPTIONS.startIndex %></td></tr>
<tr><td>Verglichen (%):</td><td><div class="processbarcon"><div class="processbar" style="width:<% $VIM.sync_status.YTOPTIONS.doneProcent %>%;"><% $VIM.sync_status.YTOPTIONS.doneProcent %>%</div></div></td></tr>
<tr><td>Added:</td><td><% $VIM.sync_status.vp_log.count_added %></td><td>Skipped:</td><td><% $VIM.sync_status.vp_log.count_skipped %></td></tr>
</table>
</fieldset>	
</div></div>
</body></html>
<%/if%>