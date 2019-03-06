<% if (count($SELLFORM.searchwords)>0) %>
<table class="table table-striped table-hover">
 <% foreach from=$SELLFORM.searchwords item=row %>
 <tr>
    <td><%$row.pname%></td>
    <td class="text-right"><a href="<%$PHPSELF%>?epage=sellform.inc&cmd=add_product&id=<%$GET.id%>&pid=<%$row.pid%>">ausw&auml;hlen</a></td>
 </tr>
 <%/foreach%>
</table>
<%/if%>