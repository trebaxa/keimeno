<form class="stdform form-inline" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:1000px">
<fieldset>	
<legend>MOD {LBL_FILES}</legend>
<table  class="table table-striped table-hover" >
<tr><td valign="top" width="200">
<table class="table table-striped table-hover">
<% foreach from=$MODUL.filelist key=ext item=fd %>
  		<% foreach from=$fd item=file %>
  		<tr>
  		<td><a href="<%$PHPSELF%>?epage=<%$epage%>&section=modstylefiles&cmd=modfileload&modfile=<%$file.file|base64encode%>"><% $file.file %></a></td>
  		<td class="text-right"><% $file.size %></td>
  		</tr>
  		<%/foreach%>	
<%/foreach%>	
  		</table>
</td><td valign="top">
<% if ($MODUL.file_name!="")%>
<label><%$MODUL.file_name%> (last mod. <%$MODUL.file_lastmod%>)</label><br><br>
<div id="mod_editor"></div>
<textarea data-theme="<%$gbl_config.ace_theme%>" name="fc" class="se-html <%$MODUL.file_ext%> form-control" style="width:100%;height:600px"><% $MODUL.file_content|hsc %></textarea>
<%/if%>
</td>
</tr></table>
<% if ($REQUEST.modfile!="") %>
	<div class="subright"><%$subbtn%></div>
<%/if%>
</fieldset>	
</div>  

  <input type="hidden" name="modfile" value="<%$REQUEST.modfile%>">
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="cmd" value="savemodfile">	
</form>

<form class="jsonform form-inline" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:1000px">
<fieldset>	
<legend>Module Files Upload</legend>
<table  class="table table-striped table-hover" >
<tr>
<td>{LBL_FILE}: </td><td><input type="file" name="datei[]"></td>
</tr>
<tr>
<td>{LA_TARGETROOT}: </td><td><select class="form-control" name="target">
<% foreach from=$MODUL.dirlist item=dir %>
 <option value="<% $dir.dir64 %>"><%$dir.dir%></option>
<%/foreach%>
</select>
</td>
</tr>

</table>
	<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>  
 
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="cmd" value="single_file_upload">	
</form>

<div id="modstylefilelist">
<%include file="modstyle.filelist.tpl"%>
</div>

<script>
function reloadfiles() {
  simple_load('modstylefilelist','<%$PHPSELF%>?cmd=modreload&epage=<%$epage%>');
}
</script>

