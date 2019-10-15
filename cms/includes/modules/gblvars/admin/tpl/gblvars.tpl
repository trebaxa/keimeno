<%*<link rel="stylesheet" href="../includes/modules/gblvars/admin/css/style.css" type="text/css"/>*%>

<div class="page-header"><h1>Globale Variablen</h1></div>

<div class="btn-group">
    <button class="btn btn-secondary" type="button" onClick="$('#js-gblvar-editor').html('');simple_load('js-gblvar-table','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_load_vars');">Alle anzeigen</button>
    <a class="btn btn-secondary" data-toggle="modal" data-target="#js-gblvar-modal"><i class="fa fa-plus"></i> neu</a>
</div>


<div id="js-gblvar-editor"></div>

<div id="js-gblvar-table"></div>

<script>
function load_gblvars() {
    $('#js-gblvar-modal').modal('hide');
    simple_load('js-gblvar-table','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_load_vars');
}
 load_gblvars();   
</script>

<!-- Modal: Neues Inlay -->
<div class="modal fade" id="js-gblvar-modal" tabindex="-1">
    <form method="post" class="jsonform" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Neues Inlay anlegen</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>

                </div><!-- /.modal-header -->
                <div class="modal-body">
                    <input type="hidden" name="epage" value="<% $epage %>">
                    <input type="hidden" name="cmd" value="ax_create_gblvars">

                    <div class="form-group">
                        <label for="titel">Name der Variable</label>
                        <input id="titel" type="text" class="form-control" value="" name="FORM[var_name]">
                    </div><!-- /.form-group -->

                </div><!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                    <% $subbtn %>
                </div><!-- /.modal-footer -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->
<!-- /Modal: Neues Inlay -->