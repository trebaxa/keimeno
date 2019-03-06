<% if ($paging.total_pages > 1) %>
<table align="right" class="table table-striped table-hover"  style="width:auto">
	<tr>
		<% if ($paging.start > 1) %>
		<td align="left" >
		<a href="<% $paging.base_link_admin %>&section=<%$section%>&cmd=<%$cmd%>&start=<% $paging.startback %>"><b>&lt;&lt; {LBL_ZURUECK}</b></a>
		</td>
		<% /if%>
		<td align="right">{LBL_PAGE}: <% $paging.akt_page %> {LBL_OF} <% $paging.total_pages %>
		<%if ($paging.next_pages) %> | {LBL_PAGES}:<% /if %> 
		<% foreach from=$paging.back_pages item=link name=pp %>
			<% if ($link.index>0) %> <a title="<%$link.index%>" href="<%$link.linkadmin%>&section=<%$section%>&cmd=<%$cmd%>"><%$link.index%></a>	<%/if%>
		<% /foreach %> 
		<strong><% $paging.akt_page %></strong>
		<% foreach from=$paging.next_pages item=link name=pp %> 
			<a title="<%$link.index%>" href="<%$link.linkadmin%>&section=<%$section%>&cmd=<%$cmd%>"><%$link.index%></a>		
		<% /foreach %> 
		<% if ($paging.start + $gbl_config.pro_max_paging < $paging.count_total) %> 
		| <a href="<% $paging.base_link_admin %>&section=<%$section%>&cmd=<%$cmd%>&start=<% $paging.newstart %>"><b>{LBL_NEXT} &gt;&gt;</b></a> 
		<% /if %>
		</td>
	</tr>
</table>
<br>
<% /if %>