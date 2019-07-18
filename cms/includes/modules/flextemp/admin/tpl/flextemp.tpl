<link rel="stylesheet" href="../includes/modules/flextemp/admin/css/style.css" type="text/css"/>

<%include file="cb.page.title.tpl" title="FlexTemplates"%>

<div class="tab-content">

    <%include file="cb.panel.header.tpl" title="FlexTemplates" class=""%>
        <div class="btn-group">
            <a class="btn btn-secondary" href="#" onclick="reload_flx_table(0)"><i class="fa fa-table"></i> Alle anzeigen</a>
            <a class="btn btn-secondary" href="#" data-toggle="modal" data-target="#new-flex-tpl"><i class="fa fa-plus"></i> Neues Flex-Template</a>     
        </div>
        
        <div id="js-flxtpls">
            <% if ($section=='edit') %>
                <%include file="flxtpl.edit.tpl"%>
            <%/if%>
        </div>
    <%include file="cb.panel.footer.tpl"%>    

</div>

<script>
   
    function reload_flx_table(reloadtree) {
         $('#new-flex-tpl').modal('hide');
        simple_load('js-flxtpls', '<%$eurl%>cmd=load_flxtpls');    
        if (reloadtree==1) {
            reload_flextpl_tree();
        }
    }
    
    <% if ($section=='' || $section=='start') %>
        reload_flx_table(0);
    <%/if%>
</script>




<!-- Modal -->
<div class="modal fade" id="new-flex-tpl" tabindex="-1" role="dialog" aria-labelledby="new-flex-tplLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="add_flxtpl" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          
          <div class="modal-header">
            <h5 class="modal-title" id="new-flex-tplLabel">Neues Flex-Template</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label>Template Name</label>
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