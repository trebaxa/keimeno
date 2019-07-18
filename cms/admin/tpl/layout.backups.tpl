<%include file="cb.panel.header.tpl" icon="fa-database" title="Backup"%>
<% if (count($LAY.backups)>0) %>
    <table class="table table-striped table-hover"  >
        <thead><tr>
    	<th>Date</th>
        <th>File</th>		
        <th>Employee</th>
		<th></th>
    </tr>
    </thead>
    <tbody>    
	<% foreach from=$LAY.backups item=row %>
    <tr>
    	<td><%$row.date%></td>
        <td><%$row.b_file%></td>
        <td><%$row.b_employee%></td>
		<td class="text-right"><button type="button" class="btn btn-secondary js-backupview" data-logid="<%$row.id%>"><i class="fa fa-eye"></i></button></td>
    </tr>  
    <%/foreach%>
    </tbody>
    </table>
    <%else%>
    <div class="alert alert-info">Keine Backups gefunden.</div>
<%/if%>
<%include file="cb.panel.footer.tpl"%>

<script>
    $( ".js-backupview" ).click(function() {
     add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=showbackup&id='+$(this).data('logid'), 900);
    });   
</script>