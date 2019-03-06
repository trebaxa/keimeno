<form enctype="multipart/form-data" id="featureform" method="POST" action="<%$PHPSELF%>" class="jsonform">
    <div class="form-group">
        <label>Titel</label>
        <input type="text" required="" value="<%$FEATURES.feature.f_title|hsc%>" class="form-control" name="FORM[f_title]">
    </div>     
    <div class="form-group">
        <label>Text</label>
        <textarea id="js-feature-editor" required="" class="form-control se-html" name="FORM[f_text]"><%$FEATURES.feature.f_text|hsc%></textarea>            
    </div>
    <div class="form-group">
        <label>FA Icon</label>
        <div class="input-group">
            <input type="text"  value="<%$FEATURES.feature.f_icon|hsc%>" class="form-control js-faicon-change" name="FORM[f_icon]">
            <div class="input-group-addon"></div>
        </div>    
    </div>  
    <div class="form-group">
        <label>Image</label>            
        <input type="file"  value="" class="form-control" name="datei">                    
    </div>  
    <div class="form-group">
        <label>Gruppe</label>
        <select name="FORM[f_gid]" class="form-control f_gid">
            <% foreach from=$FEATURES.feature_groups item=row %>
                <option <% if ($row.id==$FEATURES.feature.f_gid) %>selected<%/if%> value="<%$row.id%>"><%$row.fg_name%></option>
            <%/foreach%>
        </select>
    </div>   
    
    <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             <%$subbtn%>
    </div>
      
    <input type="hidden" name="cmd" value="save_feature">
    <input type="hidden" name="id" value="<%$FEATURES.feature.id%>">
    <input type="hidden" name="epage" value="<%$epage%>">
</form>        

<script>
$( document ).ready(function() {
    $( ".js-faicon-change" ).keyup(function() {
      $(this).next('.input-group-addon').html('<i class="js-del-faicon fa fa-'+$(this).val()+'"></i>');
    });
    
    $( ".js-faicon-change" ).click(function() {
      $(this).next('.input-group-addon').html('<i class="js-del-faicon fa fa-'+$(this).val()+'"></i>');
    });
});
</script>