<link rel="stylesheet" href="../includes/modules/features/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>features</h1></div>

<div id="js-feature-container">
    <%include file="features.main.tpl"%>
</div>

<script>
function reload_features(gid) {
    simple_load('features','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_features&gid='+gid);
}
function reload_page() {
    simple_load('js-feature-container','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_page');
    $('#feateditgroup').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
}
</script>