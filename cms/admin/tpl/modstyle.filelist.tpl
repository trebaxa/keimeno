<% if (count($MODUL.imglist)>0) %>
<form action="<%$PHPSELF%>" method="post" class="jsonform form-inline" enctype="multipart/form-data">
<div style="width:1000px">
<fieldset>	
<legend>Module Files Images</legend>
<table  class="table table-striped table-hover" >
<% foreach from=$MODUL.imglist key=ext item=fd %>
  		<% foreach from=$fd item=file %>
  		  		 <tr height="30" >
                     <td> <% foreach from=$file.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
                     <td valign="top">
                  
  		<a target="_mod" href="../<%$MODUL.MODULE_ROOT%><%$file.file%>"><% $file.file %></a>
  		</td>
  		<td class="text-right"><% $file.size %></td>
  		<td class="text-right"><% if ($file.dim.0>0) %><% $file.dim.0 %>x<% $file.dim.1 %><%else%>-<%/if%></td>
  		<td class="text-right"><% $file.ext %></td>
  		<td><img src="..<% $file.thumb %>"  alt="<% $file.file|hsc %>"></td>
  		<td class="text-right"><input type="file" name="datei[<%$file.file|md5%>]">
  		<input type="hidden" value="<%$file.file|base64encode%>" name="filenames[<%$file.file|md5%>]">
  		</td>
			</tr>
  		<%/foreach%>	
<%/foreach%>	

</table>
	<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>  
  	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="cmd" value="fileuploadimg">	
</form>
<%/if%>

<% if (count($MODUL.restfiles)>0) %>
<form action="<%$PHPSELF%>" method="post" class="jsonform form-inline" enctype="multipart/form-data">
<div style="width:1000px">
<fieldset>	
<legend>Module Files</legend>
<table  class="table table-striped table-hover" >
<% foreach from=$MODUL.restfiles key=ext item=fd %>
  		<% foreach from=$fd item=file %>
  		 <tr height="30" >
  		<td> <% foreach from=$file.icons item=picon name=cicons %><% $picon %><%/foreach%></td> 
  		<td valign="top">
  		<a target="_mod" href="../<%$MODUL.MODULE_ROOT%><%$file.file%>"><% $file.file %></a>
  		</td>
  		<td class="text-right"><% $file.size %></td>
  		<td class="text-right"><% if ($file.dim.0>0) %><% $file.dim.0 %>x<% $file.dim.1 %><%else%>-<%/if%></td>
  		<td class="text-right"><% $file.ext %></td>
  		<td><img src="..<% $file.thumb %>"  alt="<% $file.file|hsc %>"></td>
  		<td class="text-right"><input type="file" name="datei[<%$file.file|md5%>]">
  		<input type="hidden" value="<%$file.file|base64encode%>" name="filenames[<%$file.file|md5%>]">
  		</td>
			</tr>
  		<%/foreach%>	
<%/foreach%>	

</table>
	<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>  
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="cmd" value="fileuploadimg">	
</form>
<%/if%>
<% if ($GET.axcall==1) %><script>init_autojson_submit();set_ajaxdelete_icons('{LBL_CONFIRM}','<%$epage%>');</script><%/if%>