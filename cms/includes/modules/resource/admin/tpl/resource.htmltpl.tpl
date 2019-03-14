<%include file="cb.panel.header.tpl" title="HTML Vorlagen bearbeiten" class="panel-featured-primary"%>
    <div class="btn-group">
        <a class="btn btn-default" href="#" onclick="reload_htmltpl();"><i class="fa fa-table"></i> Alle anzeigen</a>
        <a class="btn btn-default" href="#" data-toggle="modal" data-target="#new-flex-tpl-html"><i class="fa fa-plus"></i> Neu</a>
    </div>
    
<div class="row">    
    <div class="col-md-12" id="js-tpledit">
       <%include file="resource.htmltpl.table.tpl"%> 
    </div>
</div>
<%include file="cb.panel.footer.tpl"%>




<!-- Modal -->
<div class="modal fade" id="new-flex-tpl-html" tabindex="-1" role="dialog" aria-labelledby="new-flex-tpl-htmlLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="add_flxhtmltpl" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$GET.id%>" name="FORM[t_ftid]" />
          
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="new-flex-tpl-htmlLabel">Neue HTML Vorlage</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label>Template Name</label>
                <input autocomplete="off" type="text" required="" value="" name="FORM[t_name]" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <%$subbtn%>
          </div>
      </form>
    </div>
  </div>
</div>