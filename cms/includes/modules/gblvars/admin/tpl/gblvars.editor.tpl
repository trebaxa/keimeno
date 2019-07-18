<%include file="cb.panel.header.tpl" title="bearbeiten"%>
<form class="jsonform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="save_var"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <input type="hidden" name="var_name" id="js-gblvar-name" value="<%$GET.id%>"/>
        <div class="row">
            <div class="col-md-6">
               <div class="form-group">
                    <label>Bezeichnung</label>
                    <input class="form-control" value="<%$GBLVARS.VAR.var_desc|sthsc%>" name="FORM[var_desc]" />
                </div>
                <div class="form-group">
                    <label>Var. Type</label>
                    <select class="form-control custom-select" name="FORM[var_type]" id="js-vartype-change">
                        <option <% if ($GBLVARS.VAR.var_type=='text') %>selected<%/if%> value="text">Textfeld</option>
                        <option <% if ($GBLVARS.VAR.var_type=='list') %>selected<%/if%> value="list">Liste</option>
                        <option <% if ($GBLVARS.VAR.var_type=='switch') %>selected<%/if%> value="switch">Schalter</option>
                        <option <% if ($GBLVARS.VAR.var_type=='mail') %>selected<%/if%> value="mail">E-Mail</option>
                        <option <% if ($GBLVARS.VAR.var_type=='password') %>selected<%/if%> value="password">Passwort</option>
                        <option <% if ($GBLVARS.VAR.var_type=='date') %>selected<%/if%> value="date">Datum</option>
                    </select>
                </div>
                <div class="form-group js-vartype-show js-vartype-list" style="display:none">
                    <label>Werte</label>
                    <input class="form-control" value="<%$GBLVARS.VAR.var_settings.list|sthsc%>" name="SETTING[list]" />     
                    <p class="help-block">| getrennte Werte</p>                               
                </div>
                <div class="form-group js-vartype-show js-vartype-switch" style="display:none">
                    <label>Radio Label 1</label>
                    <input class="form-control" value="<%$GBLVARS.VAR.var_settings.radio_value_1|sthsc%>" name="SETTING[radio_value_1]" />
                </div>
                <div class="form-group js-vartype-show js-vartype-switch" style="display:none">
                    <label>Radio Label 2</label>
                    <input class="form-control" value="<%$GBLVARS.VAR.var_settings.radio_value_2|sthsc%>" name="SETTING[radio_value_2]" />
                </div>
             </div>             
        </div>    
    <div class="btn-group">
        <button class="btn btn-danger" type="button" onClick="$('#js-gblvar-editor').html('');simple_load('js-gblvar-table','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_load_vars');">zurück</button>
        <%$subbtn%>
    </div>
</form>
<%include file="cb.panel.footer.tpl"%>

<script>
$( "#js-vartype-change" ).change(function() {
    $('.js-vartype-show').hide();
    $('.js-vartype-'+$(this).val()).fadeIn();
});
$( "#js-vartype-change" ).trigger('change');
$('#js-gblvar-table').html('');

function set_gblvar_id(var_name) {
    $('#js-gblvar-name').val(var_name);
}
</script>