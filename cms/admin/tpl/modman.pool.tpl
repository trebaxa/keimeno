<% if count($MODMAN.pool)>0 %>
    <div id="modpool">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">App Datenbank</h3><!-- /.panel-title -->
            </div><!-- /.panel-heading -->
            
            <table id="appdb" class="table table-striped">
                <thead>
                    <tr>
                        <th>App</th>
                        <th>Version</th>
                        <th>inst. Version</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead> 
                
                <% foreach from=$MODMAN.pool item=row %>
                    <% if ($row.settings.iscore!='true') %>
                    
                    <tr id="mod-<%$row.id%>" >
                        <td><% $row.e_name %></td>
                        <td>
                        <% if ($row.current_version_num>$row.e_version_num) %>
                            <span class="badge fa-yellow"><% $row.e_version %></span>
                        <%else%>
                            <% $row.e_version %>    
                        <%/if%>
                        </td>
                        <td><% $row.current_version %></td>
                        <td><%$row.e_description%></td>
                        <td class="text-right">                        
                            <% if (($row.current_version_num!=$row.e_version_num || $row.current_version_num==$row.e_version_num) && $row.installed==true) %>
                                <a class="btn btn-secondary js-updatemod" title="Update <% $row.e_name %>" href="javascript:void(0)" data-ident="<%$row.id%>">update</a>
                            <%/if%>
                            <% if ($row.installed==false) %>
                                <a class="btn btn-secondary installmod" title="Install <% $row.e_name %>" href="javascript:void(0)" data-ident="<%$row.id%>">install</a>
                            <%/if%>
                            
                        </td>
                        <td> <% if ($row.current_version_num!=$row.e_version_num) %>
                            <i class="fa fa-exclamation-triangle fa-yellow fa-2x">&nbsp;</i>
                        <%else%>
                            <i class="fa fa-check fa-green fa-2x">&nbsp;</i>
                        <%/if%>
                            </td>
                        </tr>
                        
                    <%/if%>
                <%/foreach%>
                
            </table>
        </div><!-- /.panel panel-default -->
    </div><!-- /#modpool -->
<%/if%>

<script>
    $( ".installmod" ).click(function( event ) {
        event.preventDefault();
        simple_load('modpool','<%$PHPSELF%>?epage=<%$epage%>&cmd=reminstall&modid='+$(this).data('ident'));
    });
    $( ".js-updatemod" ).click(function( event ) {
        event.preventDefault();
        jsonexec('<%$eurl%>cmd=remupdate&modid='+$(this).data('ident'),true);
    });
    $(document).ready(function() {
        $('#appdb').dataTable();
} );
</script>