<% if ($VIM.loggedin==TRUE) %>
<form onSubmit="showPageLoadInfo();" method="POST" name="qform" action="<%$PHPSELF%>">

<div style="width:600px">  
<fieldset>	
<legend>Vimeo Suche</legend>  
<input type="hidden" name="FORM[vp_stock]" value="VI">

<div id="catcontainer">
	<table  >
	<% foreach from=$VIM.query.cat_selectboxes key=qc item=catselectbox %>
	<tr>
	<td>{LA_PLEASESELECTCATEGORY} (<%$qc%>):</td>
	<td><select class="form-control custom-select" name="CIDS[]">
	<% $catselectbox %>
	</select>
	</td>
	</tr>
	<%/foreach%>
	</table>
</div>

<% include file="video.vimeo.form.tpl" %>

<div class="subright"><%$btngo%></div>

</fieldset>	
</div>  
  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="query_run">
  <input type="hidden" name="epage" value="<%$epage%>">
  <input type="hidden" name="stocktype" value="<%$REQUEST.stocktype%>">
</form> 
<%else%>
<div class="alert alert-info">Sie m&uuml;ssen sich erst bei Vimeo registrieren und die API entsprechend konfigurieren.</div>
<%/if%>