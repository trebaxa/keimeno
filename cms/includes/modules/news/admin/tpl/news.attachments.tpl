<% if (count($news_obj.filelist) > 0)%>
<div class="form-group">
	<label>Anh&auml;nge:</label>
	<table class="table table-striped table-hover" >
	<% foreach from=$news_obj.filelist item=afile %>
			<tr>
				<td><%$afile.uploadtime%></td>
				<td><a title="<%$afile.f_file%>" target="_afile" href="../<%$NEWS_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
				<td><%$afile.humanfilesize%></td>
				<td>
				<% if ($afile.thumbnail!="") %>
					<img src="..<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
				<% else %>	
				 <%$afile.f_ext%>
				<%/if%>
				<td class="text-right"><%$afile.icon_del%></td>
			</div>
			<% /foreach %>
		</table>		
</div>
<% /if %>
