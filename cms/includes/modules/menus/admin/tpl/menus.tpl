<link rel="stylesheet" href="../includes/modules/menus/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>Multi Menus</h1></div>

<div class="tab-content">

    <div class="btn-group">
        <button type="button" class="btn btn-secondary" onclick="reload_menus();"><i class="fa fa-table"></i> Alle anzeigen</button>
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addmmenu"><i class="fa fa-plus"></i> Neu</button>
    </div>
    
    <div id="js-multi-menu">
        <% if ($cmd='edit_menu') %>
            <%include file="menus.edit.tpl"%>
        <%/if%>
    </div>

</div>




<!-- Modal -->
<div class="modal fade" id="addmmenu" tabindex="-1" role="dialog" aria-labelledby="addmmenuLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form class="jsonform" method="post" action="<%$PHPSELF%>">
      <input type="hidden" name="cmd" value="save_menu" />
      <input type="hidden" name="epage" value="<%$epage%>" />
      <div class="modal-header">
        <h5 class="modal-title" id="addmmenuLabel">Neues Menü</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Menü Name</label>
            <input type="text" class="form-control" name="FORM[m_name]"/>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" onclick="$('addmmenu').modal('hide)" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
function reload_menus() {
    simple_load('js-multi-menu','<%$eurl%>cmd=reload_menus');
}
<% if ($section=='start') %>
reload_menus();
<%/if%>
</script>