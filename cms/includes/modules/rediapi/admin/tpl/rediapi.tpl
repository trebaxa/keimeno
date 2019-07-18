<div class="page-header"><h1>Redimero API Configuration</h1></div>

<div class="btn-group"><a class="btn btn-secondary" href="javascript:void(0);" onclick="clear_redi_form();">Neu anlegen</a></div>

<% if ($section=='' || $section=='start') %>
<form  class="rediform" method="POST" action="<%$PHPSELF%>">
<div class="row">  
    <div class="col-md-6">
    <legend>Configuration</legend>
        <div class="form-group">
            <label>App. Name:</label>
            <input type="text" class="form-control" size="60" value="<%$REDIAPI.API.r_name|sthsc%>" name="FORM[r_name]">
        </div>    
        <div class="form-group">
            <label>API ID:</label>
            <input type="text" class="form-control" size="60" value="<%$REDIAPI.API.r_apiid|sthsc%>" name="FORM[r_apiid]">
        </div>
        <div class="form-group">
            <label>API Key:</label>
            <input type="text" class="form-control" size="60" value="<%$REDIAPI.API.r_apikey|sthsc%>" name="FORM[r_apikey]">
        </div>
        <div class="form-group">
            <label>Server URL:</label>
            <input type="text" class="form-control" size="60" value="<%$REDIAPI.API.r_serverurl|sthsc%>" name="FORM[r_serverurl]">
        </div>     
     <div class="subright"><%$subbtn%></div>
    </div>	
</div> 
  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="save_api_keys">
  <input type="hidden" id="rediapiid" name="id" value="<%$GET.id%>">
  <input type="hidden" name="epage" value="<%$epage%>">
</form>
<%/if%>

<div id="reditable">
<% include file="rediapi.table.tpl" %>
</div>

<script>
function clear_redi_form() {
    $(".rediform :input").not(":button, :submit, :reset, :hidden").each(function () {
            this.value = '';
    });
    $('#rediapiid').val('');
}

function load_rediapis() {
    simple_load('reditable','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_axapis');
    clear_redi_form();
}

	var rediapiopt = {
		type: 'POST',
		forceSync: true,
		success: load_rediapis // post-submit callback 
	};    
	
    $('.rediform').submit(function() {
		$(this).ajaxSubmit(rediapiopt);
		return false;
	});
</script>  

