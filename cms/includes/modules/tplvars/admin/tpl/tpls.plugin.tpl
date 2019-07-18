<div class="btn-group form-inline">
<label>Bitte Vorlage w&auml;hlen:</label>
    <select class="form-control custom-select" id="tplselect" name="PLUGFORM[tplid]">
        <option value="-">- Please select -</option>
        <% foreach from=$WEBSITE.PLUGIN.data.tpllist item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.tpl_name%></option>
        <%/foreach%>
    </select>   
</div>    

<div id="tpledior"></div>




<script>
$( "#tplselect" ).change(function() {
   remove_all_tinymce();
   if ($(this).val()!="-") simple_load('tpledior','<%$PHPSELF%>?epage=tplvars.inc&cmd=loadpluginform&id='+$(this).val()+'&content_matrix_id=<% $WEBSITE.node.id %>');
});

function load_tpl_editor() {
   remove_all_tinymce();
   if ($( "#tplselect" ).val()!="-") simple_load('tpledior','<%$PHPSELF%>?epage=tplvars.inc&cmd=loadpluginform&id='+$( "#tplselect" ).val()+'&content_matrix_id=<% $WEBSITE.node.id %>'); 
}
load_tpl_editor();
</script>
