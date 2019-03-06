<table>
<% foreach from=$THORDER.whoisres item=row %>
 <tr>
    <td><%$row.0.name%></td>
    <td><% if ($row.0.orderable==-1) %>OK<%else%>-<%/if%></td>
</tr>    
 <%/foreach%>
 </table>