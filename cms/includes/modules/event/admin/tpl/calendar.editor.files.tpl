<% if (count($EVENT.edit_form.event.filelist) > 0)%>
<div class="form-group">
	<label>Anh&auml;nge:</label>
	<table class="table table-striped table-hover" >
	<% foreach from=$EVENT.edit_form.event.filelist item=afile %>
		
			<tr>
				<td><%$afile.uploadtime%></td>
				<td><a title="<%$afile.f_file%>" target="_blank" href="../<%$EVENT.edit_form.event_path%><%$afile.f_file%>"><%$afile.f_file%></a></td>
				<td><%$afile.humanfilesize%></td>
				<td>
				<% if ($afile.thumbnail!="") %>
					<img src="..<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
				<% else %>	
				 <%$afile.f_ext%>
				<%/if%>
				<td class="text-right"><%$afile.icon_del%></td>
			</tr>
			<% /foreach %>
		</table>		
	</div>
<% /if %>