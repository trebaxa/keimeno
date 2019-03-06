    <div class="page-header"><h1><i class="fa fa-columns"><!----></i> Frameworks</h1></div>

<div class="form-group">
    <label>Framework Auswahl:</label> 
    <select class="form-control" id="fwselect">
    <% foreach from=$FRAMEW.frameworks item=row %>
        <option value="<%$row.fw_number%>">Framework <%$row.fw_number%></option>
    <%/foreach%>
    </select>
</div>

<div class="alert alert-info">Hier definieren Sie für den Backend-Editor die Template Content Spots.</div>

 <%include file="cb.panel.header.tpl" icon="fa-cubes" title="Framework"%>
    
    <form class="stdform form-inline" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="save_framework">
        <input type="hidden" name="epage" value="<%$epage%>">
        
        <div id="framelayout" class="row">
            <div class="col-md-12 text-center">
                <%include file="framework.editor.tpl"%>
            </div>
        </div>
        
        <div id="framelayout" class="row">
            <div class="col-md-12 text-center">
                <%$subbtn%>
            </div>
        </div>
    
    </form>
<%include file="cb.panel.footer.tpl"%>
<script>
    $('#fwselect').change(function() {
       simple_load('framelayout','<%$PHPSELF%>?epage=<%$epage%>&cmd=axloadfw&id='+$("#fwselect option:selected").val()); 
    });
</script>
