<%*<link rel="stylesheet" href="../includes/modules/gblvars/admin/css/style.css" type="text/css"/>*%>

<div class="page-header"><h1>Globale Variablen</h1></div>

<div class="btn-group">
    <button class="btn btn-secondary" type="button" onClick="$('#js-gblvar-editor').html('');simple_load('js-gblvar-table','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_load_vars');">Alle anzeigen</button>
</div>


<div id="js-gblvar-editor"></div>

<div id="js-gblvar-table"></div>

<script>
    simple_load('js-gblvar-table','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_load_vars');
</script>