
<form id="stdformval" method="POST" name="qform" action="<%$PHPSELF%>">

<div style="width:600px">  
<fieldset>	
<legend>{LA_YOUTUBEQUERY}</legend>  
<input type="hidden" name="FORM[vp_stock]" value="YT">

<div id="catcontainer">
	<table   class="table table-striped table-hover">
	<% foreach from=$VIM.query.cat_selectboxes key=qc item=catselectbox %>
	<tr>
	<td width="300">{LA_PLEASESELECTCATEGORY} (<% math equation="x + y" x=$qc y=1 %>):</td>
	<td><select class="form-control" name="CIDS[]">
	<% $catselectbox %>
	</select>
	</td>
	</tr>
	<%/foreach%>
	</table>
</div>

<% include file="video.yt.form.tpl" %>

<div class="subright"><%$btngo%></div>

</fieldset>	
</div>  
  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="query_run">
  <input type="hidden" name="stocktype" value="<%$REQUEST.stocktype%>">
  <input type="hidden" name="epage" value="<%$epage%>">
</form> 

