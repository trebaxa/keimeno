  <%include file="cb.panel.header.tpl" title="Benutzergruppen"%>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#js-addgroup">Neue Gruppe</button>
    <table class="table table-striped table-hover" >
        <thead>
            <th>#</th>
            <th>Gruppe</th>
            <th></th>
        </thead>
        
        <% foreach from=$CUSTGROUPS.custgroups item=row %>
            <tr>
                <td><%$row.GID%></td>
                <td><% if ($row.GID==1000) %><strong><%$row.groupname%> [<%$row.KCOUNT%>]</strong><%else%><%$row.groupname%> [<%$row.KCOUNT%>]<%/if%></td>
                <td class="text-right">
                    <div class="btn-group">
                        <a href="#" data-gid="<%$row.GID%>" class="btn btn-primary addkundeclick"><i class="fa fa-plus"><!----></i></a>
                        <% foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%>
                    </div>
                </td>
            </tr>
        <%/foreach%>
    
    </table>
<%include file="cb.panel.footer.tpl"%>
<script>
    $( ".addkundeclick" ).css('cursor','pointer');
    $( ".addkundeclick" ).click(function() {
        $('#custgroupid').val($(this).data('gid'));
        $('#js-addkunde').modal('show');
    });
</script>


<%include file="cb.panel.header.tpl" title="Gruppen"%>
    
    <div class="alert alert-info">Pro Zeile ein Bereichname.</div>
    <form method="post" action="<%$PHPSELF%>" class="jsonform">
        <div class="form-group">
            <label for="">Kollektion</label>
            <select class="form-control custom-select" name="collid"><option value="0">-</option><% foreach from=$CUSTGROUPS.collections item=col %>	<option value="<%$col.id%>"><%$col.col_name%></option>	<%/foreach%></select>
        </div><!-- /.form-group -->
            
                <textarea class="form-control" name="grouplist" rows="10" cols="60"></textarea>
                <%$addbtn%>

        <input type="hidden" name="cmd" value="add_group_list">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="section" value="<%$section%>">
    </form>
<%include file="cb.panel.footer.tpl"%>