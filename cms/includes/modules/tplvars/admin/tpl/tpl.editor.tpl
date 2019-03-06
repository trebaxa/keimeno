<div style="display:none" id="tpleditoredit" class="row">

<div class="col-md-6">
<h3>Editor</h3>
<form action="<%$PHPSELF%>" method="POST" class="jsonform ">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="savetpl">
    <input type="hidden" name="id" value="<%$TPLVARS.loadedtpl.id%>">
    <div class="form-group">
        <label>Vorlage Name:</label>
        <input type="text" class="form-control" name="FORM[tpl_name]" value="<%$TPLVARS.loadedtpl.tpl_name|st|hsc%>">
    </div>    
    <div class="form-group">
        <label>Template Frontend</label>
        <textarea class="form-control se-html" name="FORM[tpl_content]"><%$TPLVARS.loadedtpl.tpl_content|hsc%></textarea>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea class="form-control" rows="3" name="FORM[tpl_description]"><%$TPLVARS.loadedtpl.tpl_description|sthsc%></textarea>
    </div>    
        
    <div class="subright"><%$subbtn%></div>
</form>
</div>


<div class="col-md-6">
        <h3>Variable hinzufügen</h3>
<div class="btn-group form-inline">
<a href="javascript:void(0)" class="btn btn-default pull-right" onclick="add_var_to_tpl()">{LBL_ADD}</a>
        <select class="form-control" id="varselect">
<% foreach from=$TPLVARS.vars item=row %>
    <option value="<%$row.id%>"><%$row.var_name%></option>
<%/foreach%>
</select>
</div>        
<div id="addvars"></div>
</div><!--col-->
</div>


<script>
    function add_var_to_tpl() {
        simple_load('addvars','<%$PHPSELF%>?epage=<%$epage%>&cmd=addvartpl&varid='+$('#varselect').val()+'&id='+<%$TPLVARS.loadedtpl.id%>);
    }
    
    function reloadaddedvars() {
        simple_load('addvars','<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadaddedvars&id='+<%$TPLVARS.loadedtpl.id%>);
    }
    reloadaddedvars();
</script>