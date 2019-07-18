<div class="btn-group">
    <% if (count($FEATURES.feature_groups) >0)%>
        <a class="btn btn-secondary" href="javascript:void(0);" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=edit_feature&id=0', 'Feature neu anlegen');">Neues Feature</a>
    <%/if%>
    <a class="btn btn-secondary" href="javascript:void(0);" onclick="$('#featgroupid').val('0');$('#feateditgroup').modal('show')">Neue Gruppe</a>
    <% if (count($FEATURES.feature_groups) >0)%>
    <div class="form-group">
        <label>Gruppe</label>
        <select id="js-fgroup-change" class="form-control">
            <option value="0">- alle -</option>
            <% foreach from=$FEATURES.feature_groups item=row %>
                <option value="<%$row.id%>"><%$row.fg_name%></option>
            <%/foreach%>
        </select>
    </div>
    <%/if%>
</div>

<div id="features">
    <% include file="features.table.tpl" %>
</div>

<!-- Modal -->
<div class="modal fade" id="feateditgroup" tabindex="-1" role="dialog" aria-labelledby="feateditgroupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form id="featuregfroupform" method="POST" action="<%$PHPSELF%>" class="jsonform">
      <div class="modal-header">
        <h5 class="modal-title" id="feateditgroupLabel">Gruppe anlegen</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Gruppen Name</label>
            <input type="text" required value="" class="fg_name form-control" name="FORM[fg_name]">
        </div>    
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
        <input type="hidden" name="section" value="<%$REQUEST.section%>">
        <input type="hidden" name="cmd" value="save_fgroup">
        <input type="hidden" id="featgroupid" class="id" name="id" value="">
        <input type="hidden" name="epage" value="<%$epage%>">
      </form>
    </div>
  </div>
</div>


<script>
$( "#js-fgroup-change" ).change(function() {
  reload_features($(this).val());
});




</script>