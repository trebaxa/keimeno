 <%include file="cb.panel.header.tpl" title="Backups"%>  
      
    <% if (count($GBLTPL.backups)>0) %>
        <table class="table table-striped table-hover"  >
            <thead><tr>
            <th>Date</th>
            <th>Employee</th>       
            <th></th>
        </tr></thead>  
        <tbody>  
            <% foreach from=$GBLTPL.backups item=row %>
                <tr>
                    <td><%$row.date%></td>
                    <td><%$row.b_employee%></td>
                    <td><a href="javascript:void(0)" class="backupview" rel="<%$row.id%>"><i class="fa fa-eye"></i></a></td>
                </tr>  
            <%/foreach%>
        </tbody>
        </table>
        <%else%>
        <div class="bg-info text-info"><p class="text-info">Keine Backups gefunden.</p></div>
    <%/if%>
    <script>
        $( ".backupview" ).click(function() {
         add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=showbackup&id='+$(this).attr('rel'), 900);
        });   
    </script>

<%include file="cb.panel.footer.tpl"%>