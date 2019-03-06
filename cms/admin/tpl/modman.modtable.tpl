<% if count($MODMAN.allmods_arr)>0 %>

<!-- Modal -->
<div class="modal fade" id="unimod" tabindex="-1" role="dialog" aria-labelledby="unimodLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="unimodLabel">Deinstallation</h4>
      </div>
      <div class="modal-body">
         <h3>Soll die App wirklich deinstalliert werden?</h3>
         <p class="alert alert-danger">Alle System Templates zu dieser App werden gelöscht!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Abbruch</button>
        <button data-ident="" class="btn btn-danger js-uninstallbtn">Ja</button>
      </div>
    </div>
  </div>
</div>
    
   <%include file="cb.panel.header.tpl" icon="fa-cubes" title="Installierte Apps"%>
    
        <form action="<%$PHPSELF%>" method="post" class="jsonform form-inline">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>App</th>
                        <th>Version</th>
                        <th></th>
                        <th></th>
                        <th>Uninstall</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead> 
                
                <% foreach from=$MODMAN.allmods_arr item=row %>
                    <% if ($row.settings.iscore!='true') %>
                        <tr id="mod-<%$row.settings.id%>" >
                            <td><% $row.settings.module_name %></td>
                            <td><% $row.settings.version %></td>
                            <td><i class="fa fa-circle <% if ($row.settings.active=='true') %>text-success<%else%>text-danger<%/if%>">&nbsp;</i></td>
                            <td class="text-right">
                                <input class="modactive" <% if ($row.settings.active=='true') %>checked<%/if%> data-active="<%$row.settings.active%>" type="checkbox" name="MODS[]" value="<%$row.settings.id%>">
                                <input type="hidden" value="<%$row.settings.id%>" name="allmods[]">
                            </td>
                            <td>
                                <%if ($row.uninstallable==true) %>
                                    <a href="javascript:void(0)" title="deinstall" class="js-moduninst" data-ident="<%$row.settings.id%>"><i class="fa fa-times fa-lg text-danger"><!----></i></a>
                                <%else%>
                                    -
                                <%/if%>
                            </td>
                            <td><%$row.settings.description%></td>
                            <td class="text-right">                        
                                <a class="btn btn-default js-updatemod" title="Update <% $row.settings.module_name %>" href="javascript:void(0)" data-ident="<%$row.settings.id%>">update</a>
                            </td>
                        </tr>
                    <%/if%>
                <%/foreach%>
            </table>
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="activate_mod">
            <div class="form-feet"><%$subbtn%></div><!-- /.form-feet -->
        </form>
    <%include file="cb.panel.footer.tpl"%>
    
    <script>
    $( ".modactive" ).click(function() {
        if ($(this).data('active')==true) {
            $(this).data('active',false);    
            $(this).parent().prev().find("i").removeClass("fa-green").addClass('fa-red');
        } else {
            $(this).data('active',true);
            $(this).parent().prev().find("i").removeClass("fa-red").addClass('fa-green');
        }
    });
    </script>
<%/if%>

<script>
    $( ".js-updatemod" ).click(function( event ) {
        event.preventDefault();
        jsonexec('<%$eurl%>cmd=remupdate&modid='+$(this).data('ident'),true);
    });
    $( ".js-moduninst" ).click(function( event ) {
        event.preventDefault();
        $('#unimod').modal('toggle');                
        $('.js-uninstallbtn').attr('data-ident',$(this).data('ident'));
    });
    
    
    $( ".js-uninstallbtn" ).click(function( event ) {
        event.preventDefault();
        execrequest('<%$eurl%>cmd=uninstall_mod&mod_id='+$(this).data('ident'));
        $('#mod-'+$(this).data('ident')).fadeTo(400, 0, function () { 
            $(this).remove();
        });
        $('#unimod').modal('toggle');
        show_msg('Deinstall complete');   
    });
    
    function reload_menu() {
        simple_load('menu_reload_cont','<%$eurl%>cmd=reloadmenu');    
    }
</script>