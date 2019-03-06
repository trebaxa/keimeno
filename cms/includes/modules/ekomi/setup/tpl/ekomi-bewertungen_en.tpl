<% if (count($EKOMI.items)>0) %>
<h2>eKomi Bewertungen</h2>
<table class="tab_std">
<% foreach from=$EKOMI.items item=row %>
            <tr ">
                    <td><% $row.date%></td>
                    <td><% $row.customer%></td>
                    <td width="160"><%section name=foo start=0 loop=$row.stars step=1%><i class="fa fa-star fa-green">&nbsp;</i><%/section%></td>
                    <td><% $row.review%></td>
            </tr>
<%/foreach%>
</table>


<%else%>
Keine EintrÃ¤ge vorhanden.
<%/if%>
