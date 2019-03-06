<tr>
    <td><a href="<%$PHPSELF%>?epage=inlayadmin.inc&id=<%$row.i_iid%>&aktion=edit&cmd=edit&uselang=<%$GET.uselang%>"><% $row.description %></a></td>
    <td>
        <% if ($row.i_pos==1) %>oben<%/if%>
        <% if ($row.i_pos==2) %>unten<%/if%>
     </td>
     <td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
     <a href="<%$PHPSELF%>?epage=inlayadmin.inc&id=<%$row.i_iid%>&aktion=edit&cmd=edit&uselang=<%$GET.uselang%>"><img alt="edit" src="./images/page_white_edit.png" title="edit"  /></a></td>
</tr>