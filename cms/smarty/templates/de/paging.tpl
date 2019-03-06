<% if ($paging.total_pages > 1) %>
<table class="tab_std"  width="99%"><tr>
<% if ($paging.start > 0) %>
<td align="left" width="20%">
 <a href="<% $paging.base_link %>&start=<% $paging.startback %>"><b>&lt;&lt; {LBL_ZURUECK}</b></a>
</td> 
 <% /if%>
<td align="right">
 {LBL_PAGE}: <% $paging.akt_page %> {LBL_OF} <% $paging.total_pages %> 
 <%if ($paging.next_pages) %> | {LBL_PAGES}:<% /if %>
 
 <% foreach from=$paging.back_pages item=ppage_link name=pp %> 
 <% $ppage_link %>
<% /foreach %>
<strong><% $paging.akt_page %></strong>
<% foreach from=$paging.next_pages item=ppage_link name=pp %> 
 <% $ppage_link %>
<% /foreach %>
<% if ($paging.start + $gbl_config.pro_max_paging < $paging.product_count_total) %>
 | <a href="<% $paging.base_link %>&start=<% $paging.newstart %>"><b>{LBL_NEXT} &gt;&gt;</b></a>
 </td>
 <% /if %>
</tr></table><br>
<% /if %>
