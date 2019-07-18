<div class="page-header"><h1>Inhalte verwalten</h1></div>

<div class="tab-content">

    <%include file="cb.panel.header.tpl" title="Meine Inhalte" class=""%>
        <div class="btn-group">
            <a class="btn btn-secondary" href="#" onclick="reload_rsrc_table(0)"><i class="fa fa-table"></i> Meine Inhalte</a>
            <a class="btn btn-secondary" href="#" data-toggle="modal" data-target="#new-flex-tpl"><i class="fa fa-plus"></i> Neuer Inhalt</a>     
        </div>
        
        <div id="js-flxtpls">
            <% if ($section=='edit') %>
                <%include file="resource.edit.tpl"%>
            <%/if%>
        </div>
    <%include file="cb.panel.footer.tpl"%>    

</div>

<script>
   
    function reload_rsrc_table(reloadtree) {
         $('#new-flex-tpl').modal('hide');
        simple_load('js-flxtpls', '<%$eurl%>cmd=load_rsrctable');    
        if (reloadtree==1) {
            reload_flextpl_tree();
        }
    }
    
    <% if ($section=='' || $section=='start') %>
        reload_rsrc_table(0);
    <%/if%>
</script>




<!-- Modal -->
<div class="modal fade" id="new-flex-tpl" tabindex="-1" role="dialog" aria-labelledby="new-flex-tplLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="add_rsrc" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          
          <div class="modal-header">
            <h5 class="modal-title" id="new-flex-tplLabel">Neuen Inhalt erstellen</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label>Inhalt Title</label>
                <input autocomplete="off" type="text" required="" value="" name="FORM[f_name]" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" onclick="$('#new-flex-tpl').modal('hide');" class="btn btn-primary">{LA_SAVE}</button>
          </div>
      </form>
    </div>
  </div>
</div>