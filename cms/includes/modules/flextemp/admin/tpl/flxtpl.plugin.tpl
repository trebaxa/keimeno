<% if (count($WEBSITE.PLUGIN.result.templates)>0)%>

<div class="row">
    <div class="col-md-6">
            <div class="form-group">
            <label>Vorlagen Auswahl:</label>
            <select class="form-control" id="js-flxchange" name="PLUGFORM[flxtid]">
                <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
                    <option <% if ($WEBSITE.node.tm_plugform.flxtid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
                <%/foreach%>
            </select>
        </div>
    </div>
    <div class="col-md-6" id="js-tplsel">
        
    </div>
</div>

   




<script>
$( "#js-flxchange" ).change(function() {
   simple_load('js-tplsel','<%$PHPSELF%>?epage=flextemp.inc&content_matrix_id=<% $WEBSITE.node.id %>&cmd=plugin_load_tpls&flxid='+$(this).val());
});

reload_flxtpl_plugin();


function reload_flxtpl_plugin() {
    simple_load('js-tplsel','<%$PHPSELF%>?epage=flextemp.inc&content_matrix_id=<% $WEBSITE.node.id %>&cmd=plugin_load_tpls&flxid='+$('#js-flxchange').val());    
}    

function load_flxtpl_editor() {
    $('#modal_frame').modal('hide');
    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&id='+$('#js-flextplid').val()+'&section=edit&cmd=ax_editflextpl');
}

function reload_dataset(showtab,gid) {
    $('#new-flex-tpl').modal('hide');     
    if (parseInt(gid)==0){
        var gid = parseInt($('#js-flx-groupid').val());
    }    
    var url ='<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&gid='+gid+'&content_matrix_id=<% $WEBSITE.node.id %>&cmd=reload_dataset&flxid='+$('#js-flxchange').val()+'&showtab='+showtab;
    simple_load('js-after-plugin-editor', url);    
}

</script>
<%else%>
    <div class="alert alert-info">Bitte erst Flex-Template anlegen.</div>
<%/if%>