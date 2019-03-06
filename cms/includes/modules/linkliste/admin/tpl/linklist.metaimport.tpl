	<h3>Start analyse</h3>
	<form action="<%$PHPSELF%>" method="post">
	<input type="hidden" name="epage" value="<%$epage%>">	
	<input type="hidden" name="cmd" value="save_metas">	
  <% if (count($BALINK.meta_rl)>0) %>
  <table class="table table-striped table-hover">		
	<% foreach from=$BALINK.meta_rl item=rl %>      
		URL: <a href="<% $rl.url %>" target="_blank"><% $rl.url %></a>
		<thead><tr><th colspan="2"><% $rl.title %></th></tr></thead>
		<tr><td>Importierte Daten speichern? </td><td><input type=checkbox value="<% $rl.id %>" <% if ($rl.metas.title!="") %>checked<%/if%> name="metaids[]">{LBL_YES} </td></tr>
		<tr><td>Website {LBL_DELETE}:</td><td> <input type="checkbox" value="<% $rl.id %>" <% if ($rl.metas.metaTags.description.value=="" && $rl.metas.title=="") %>checked<%/if%> name="metaidsdel[]"> {LBL_YES}</td></tr> 
		<tr><td>Homepage Titel:</td><td><input type="text" class="form-control"  size="80" name="ROW[<% $rl.id %>][title]" value="<% $rl.metas.title %>"></td></tr>
		<tr><td>{LBLA_DESCRIPTION}:</td><td><input type="text" class="form-control"  size="80" name="ROW[<% $rl.id %>][sp_comment]" value="<%$rl.metas.metaTags.description.value|hsc%>"></td></tr>
		<tr><td>Keywords:</td><td><input type="text" class="form-control"  size="80" name="ROW[<% $rl.id %>][keywords]" value="<% $rl.metas.metaTags.keywords.value|hsc %>"></td></tr>
		<tr class="singlelinebottom"><td>Email:</td><td><input type="text" class="form-control"  size="80" name="ROW[<% $rl.id %>][pub_email]" value="<% $rl.metas.metaTags.publisheremail.value|hsc %>"></td></tr>
		
	<%/foreach%>
  </table>
  <%$subbtn%></form>	
	<%else%>
	 <div class="bg-info text-info">Keine Links markiert.</div>
	<%/if%>