<h2>Referenz Kontakte</h2>
<% if (count($REFLIST.links)>0) %>
<table class="tab_std">
<thead>
<tr class="trheader">
    <th></th>
    <th>Firma</th>
    <th>Strasse</th>
    <th>PLZ</th>
    <th>Ort</th>
    <th>Tel.</th>
    <th>Homepage</th>
    <th></th>
</tr> 
</thead>
<tbody>
<% foreach from=$REFLIST.links item=row %>
 <tr ">
    <td><img src="<%$row.thumb%>" class="thumb"></td>
    <td><%$row.r_firma%></td>
    <td><%$row.r_street%></td>
    <td><%$row.r_plz%></td>
    <td><%$row.r_ort%></td>
    <td><%$row.r_tel%></td>
    <td><a href="<%$row.r_url%>" target="_blank"><%$row.r_url%></a></td>
</tr>    
<%/foreach%>
</tbody>
</table>
<%else%>
<p class="text-info">Noch keine Ref.-Links hinzugefÃ¼gt.</p>
<%/if%>