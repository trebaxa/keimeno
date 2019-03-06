<h1>{LBL_SEARCHRESULT}</h1>
<% if (count($searchresult)>0) %>
<table width="100%" >
    <tbody>
       <% foreach from=$searchresult item=sitem %>
        <tr>
            <td><a href="<%$sitem.url%>" title="<%$sitem.title%>"><%$sitem.title%></a><br>
            <%$sitem.content%> <br>
            </td>
        </tr>
       <%/foreach%>
    </tbody>
</table>
<%else%>
<h1>Keine Treffer</h1>
<%/if%>