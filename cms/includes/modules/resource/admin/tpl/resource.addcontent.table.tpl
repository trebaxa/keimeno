<table class="table table-hover">
    <tbody id="sortable" class="ui-sortable">
        <% foreach from=$RESOURCE.content_table item=row %>
            <tr id="<%$row.id%>" class="ui-state-default ui-sortable-handle">
                <td class="sort-col"><i class="fa fa-sort" aria-hidden="true"></i></td>
                <td><a href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>','Inhalt bearbeiten')"><%$row.c_label%></a></td>
                <td><a href="<%$row.link_resrc.pi_link%>" title="<%$row.link_resrc.pi_link%>" target="_blank"><%$row.link_resrc.pi_link%></a></td>
                <td>
                    <% foreach from=$RESOURCE.tables item=table%>
                        <a title="<%$table.f_name|sthsc%>" class="btn btn-secondary btn-sm" href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_datasets&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>&langid=1&table=<%$table.f_table%>','Datensätze <%$table.f_name|sthsc%> bearbeiten')"><i class="fas fa-database"></i> <%$table.f_name|st%></a>
                    <%/foreach%>
                </td>
                <td class="col-md-2 text-right">
                   <div class="btn-group">
                    <a class="btn btn-secondary" href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>&langid=1','Inhalt bearbeiten')"><i class="far fa-edit"></i></a>                    
                    <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
                   </div> 
                </td>
            </tr>
        <%/foreach%>
    </tbody>
</table> 
