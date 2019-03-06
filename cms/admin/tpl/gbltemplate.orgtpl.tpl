<% if ($cmd=='show_org_tpl') %>
<h3>Original App Templates</h3>
<% if (count($GBLTPL.tplfiles)>0 )%>
<div class="form-group">
    <label>Select Template:</label>
    <select class="form-control modfiletpl">
    	<option <% if ($GET.mod==$ml.mod_id) %>selected<%/if%> value="<%$file%>">- please select -</option>
    	<% foreach from=$GBLTPL.tplfiles item=file %>
    	<option <% if ($GET.modfile==$file) %>selected<%/if%> value="<%$file%>"><%$file%></option>
    	<%/foreach%>
    	</select>
</div>        
<%else%>
    <div class="alert alert-info">Noch kein Template hinterlegt</div>
<%/if%>
        <br><br><br>
    
    <div id="tplorgeditor"></div>
    
<script>
$( ".modfiletpl" ).change(function() {
    simple_load('tplorgeditor','<%$PHPSELF%>?epage=gbltemplates.inc&cmd=loadorgtpl&tid=<%$GET.tid%>&modfile='+$(this).val());
});
</script>
<%/if%>

<% if ($cmd=='loadorgtpl') %>
<textarea class="form-control se-html" data-theme="<%$gbl_config.ace_theme%>"><%$GBLTPL.tplfilecontent|hsc%></textarea>
<script>set_script_editor();</script>
<%/if%>