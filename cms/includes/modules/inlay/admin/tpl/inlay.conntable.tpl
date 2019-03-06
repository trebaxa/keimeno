<% if (count($INLAY.i_connections )>0) %>
<h3>Erscheint auf:</h3>
<table class="table table-striped table-hover">
<thead><tr>
<th>Webseite</th>
<th>Position</th>
<th></th>
</tr></thead>
 <% foreach from=$INLAY.i_connections item=row %>
 	<tr>
     <td>
     <a href="<%$PHPSELF%>?epage=websitemanager.inc&uselang=<%$GET.uselang%>&aktion=edit&id=<%$row.tm_tid%>"><% $row.description %></a>
     </td>
     <td>
        <% if ($row.i_pos==1) %>oben<%/if%>
        <% if ($row.i_pos==2) %>unten<%/if%>
     </td>
     <td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
    </tr>
  <%/foreach%>  
</table>
<%/if%>