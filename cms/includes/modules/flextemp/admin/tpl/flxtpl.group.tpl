<%include file="cb.panel.header.tpl" title="Bereiche bearbeiten" class="panel-featured-primary"%>
<div class="btn-group">
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="reload_groups(<%$GET.id%>);"><i class="fa fa-table"></i> Gruppen</a>
    <a class="btn btn-secondary" data-toggle="modal" data-target="#js-new-group" href="#"><i class="fa fa-plus"></i> Neue Gruppe</a>   
</div>

<div id="js-flexgroup-table">
    <%include file="flxtpl.group.table.tpl"%>
</div>

<%include file="cb.panel.footer.tpl"%>

<!-- Modal -->
<div class="modal fade" id="js-new-group" tabindex="-1" role="dialog" aria-labelledby="js-new-groupLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="add_group" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$GET.id%>" name="FORM[g_ftid]" />
          
          <div class="modal-header">
            <h5 class="modal-title" id="js-new-groupLabel">Neue DataSet Gruppe</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label>Gruppen Name</label>
                    <input autocomplete="off" type="text" required="" value="" name="FORM[g_name]" class="form-control" />
                </div>                
                
          </div><!--body-->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" onclick="$('#js-new-group').modal('hide');" class="btn btn-primary">{LA_SAVE}</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    function reload_groups(ftid) {
        console.log('<%$eurl%>cmd=reload_groups&ftid='+ftid);
        simple_load('js-flexgroup-table"','<%$eurl%>cmd=reload_groups&ftid='+ftid);
    } 
</script>