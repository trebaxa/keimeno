<% if count($searchresult)>0 %>
<div style="width:800px">
<h3>Suchergebnis (<% $searchresult.totalfound %>)</h3>

<table class="table table-striped table-hover">
	<% foreach from=$searchresult.res_tab item=sritem %>
		<tr>
		<td><a href="javascript:void(0)" onclick="std_load_gbltpl(<%$sritem.TID%>,<%$sritem.lang_id%>)"><% $sritem.description %></a></td>
		<td><% $sritem.foundcount %></td>
		<td><%$sritem.label%></td>
        <td><img src="<% $sritem.thumb %>" ></td>
	</tr>
	<%/foreach%>
</table>

	</div>
<%else%>
 <% if ($cmd=='search') %><div class="bg-info text-info"> {LBL_NOENTRIES} </div><%/if%>
<%/if%>