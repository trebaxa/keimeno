<% if ($cmd!="sync") %>
<div class="btn-group">
<a class="btn btn-default" href="<%$PHPSELF%>?section=<%$section%>&cmd=sync&epage=<%$epage%>">Sync. mit Vimeo</a>
<% if ($VIM.loggedin==FALSE) %>
  <a class="btn btn-default" href="<% $VIM.vi_authlink %>">authenticate Vimeo application</a>
<%/if%>
</div>
<%/if%>

<% if ($cmd==load_via_videos) %>
 <% if (count($VIM.video_list)>0) %>
 <form onSubmit="showPageLoadInfo();" method="POST" name="qform" action="<%$PHPSELF%>">
<div style="width:900px">  
<fieldset>	
<legend>Vimeo Suche</legend>  	
    <table class="table table-striped table-hover" >
	<thead><tr>
		<th width="100"></th>
		<th width="200">Titel</th>
		<th width="100">L&auml;nge</th>
		<th width="300">Beschreibung</th>
        <th>Kategorie</th>
		<th>Upload Datum</th>
		<th></th>
	</tr></thead>	
	<% foreach from=$VIM.video_list item=file %>
	<tr>
	 <td><img width="100" src="<% $file.v_thumbnailurl  %>" ></td>
	 <td><a target="_video" href="../index.php?page=9810&id=<% $file.v_videoid %>&cmd=load_video_fe"><% $file.v_videotitle %></a></td>
	 <td><% $file.v_duration %></td>
	 <td><% $file.v_videodescription|truncate:300  %></td>
     <td>
     <select class="form-control" name="CIDS[<% $file.v_videoid %>]">
     <option <% if (0==$file.vcm_cid) %>selected<%/if%> value="0">- nicht zugeordnet -</option>
     <% foreach from=$VIM.cat_selectbox_arr key=catid item=catname %>
       <option <% if ($catid==$file.vcm_cid) %>selected<%/if%> value="<%$catid%>"><% $catname %></option>
      <%/foreach%>
      </Select>
     </td>
	 <td><% $file.v_recorded_ger %></td>
	 <td> <% foreach from=$file.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
	</tr>
	<%/foreach%>
	</table> 
<div class="subright"><%$subbtn%></div>

</fieldset>	
</div>     
  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="save_via_videos">
  <input type="hidden" name="epage" value="<%$epage%>">
</form> 	 
	<%else%>
<div class="bg-info text-info">No results.</div>
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