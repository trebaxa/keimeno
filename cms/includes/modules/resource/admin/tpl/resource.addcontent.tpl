<div class="page-header"><h2><%$RESOURCE.flextpl.f_name%></h2></div>

<%*$RESOURCE.flextpl|echoarr*%>

<div class="btn-group">
    <a href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$GET.flxid%>','Inhalt hinzufügen')" class="btn btn-primary"><i class="fa fa-plus"></i> hinzufügen</a>
</div>

<%include file="cb.panel.header.tpl" title="`$RESOURCE.flextpl.f_name`"%>
         
      <div class="table-responsive">   
<table class="table table-hover">
    <tbody id="sortable" class="ui-sortable">
        <% foreach from=$RESOURCE.content_table item=row %>
            <tr id="<%$row.id%>" class="ui-state-default ui-sortable-handle">
                <td class="sort-col"><i class="fa fa-sort" aria-hidden="true"></i></td>
                <td><a href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>','Inhalt bearbeiten')"><%$row.c_label%></a></td>
                <td class="col-md-2 text-right">
                   <div class="btn-group">
                    <a class="btn btn-default" href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>','Inhalt bearbeiten')"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-default" href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_datasets&flxid=<%$row.c_ftid%>&content_matrix_id=<%$row.id%>','Datensätze bearbeiten')"><i class="fa fa-database"></i></a>
                    <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
                   </div> 
                </td>
            </tr>
        <%/foreach%>
    </tbody>
</table>
    
</div>
<%include file="cb.panel.footer.tpl"%>

<script>

$( function() {
    $( "#sortable" ).sortable({
        placeholder: "highlight",
         cursor: 'move',
        update: function(event, ui) {
          var ids = $(this).sortable('toArray').toString();
          jsonexec('<%$eurl%>cmd=sort_content_table&ids='+ids);
       }
    
    });
});

function reload_resource(id) {
    simple_load('admincontent','<%$eurl%>cmd=load_resource&flxid='+id);    
}   

function reload_dataset(content_matrix_id) {   
    var url ='<%$eurl%>content_matrix_id='+content_matrix_id+'&cmd=reload_dataset&flxid=<%$GET.flxid%>';
    simple_load('showboxcontent', url);    
}
</script>

