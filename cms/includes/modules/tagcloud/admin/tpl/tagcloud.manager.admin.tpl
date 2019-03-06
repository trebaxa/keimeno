<div class="page-header"><h1>Tag Cloud Manager</h1></div>
<% if ($aktion=='') %>
<div class="row form-inline">
    <div class="col-md-12 text-right">
        {LBL_LANGUAGE}: <%$langselect%>
    </div>
</div>
        
<% if (count($tagcloudobj.words)>0) %>
<div class="row">
<div class="col-md-6">
 <table class="table table-striped table-hover">
 <tr><td>Anzahl W&ouml;rter</td><td class="text-right"><%$tagcloudobj.wordcount%></td></tr>
 <tr><td>Genehmigte:</td><td class="text-right"><%$tagcloudobj.approved%></td></tr>
 <tr><td>nicht genehmigte:</td><td class="text-right"><%$tagcloudobj.notapproved%></td></tr>
 </table>
</div>
<div class="col-md-6">
 <% foreach from=$tagcloud.words item=tagword %>
<a style="background-color: transparent;font-size:<%$tagword.fontsize%>px;color: rgb(<%$tagword.fontcolor%>, <%$tagword.fontcolor%>, <%$tagword.fontcolor%>)" target="_tagcloud" href="<%$tagword.link%>" title="<%$tagword.word%>"><% $tagword.word %></a>
<%/foreach%>
</div>
</div>
<%/if%>

<% if ($tagcloudobj.notapproved > 0) %>
<h3>zu genehmigende Tags</h3>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">


<table  class="table table-striped table-hover" >
    	<thead><tr>
    		<th></th>	
    		<th><a href="<%$PHPSELF%>?epage=<%$epage%>&tagorder=TAGNAME">Wort</a></th>
    		<th><a href="<%$PHPSELF%>?epage=<%$epage%>&tagorder=TAGCOUNT">Anzahl</a></th>
    		<th>eingetragen am</th>
    		<th>Optionen</th>
    		<th></th>
    	</tr></thead>
    <% foreach from=$tagcloudobj.words item=tagword name=gloop %>
    <% if ($tagword.tag_approved==0) %>
    	 	<tr>
    	  	<td><input class="checkall" type="checkbox" name="tagids[]" value="<%$tagword.TAGID%>"></td>
    	  	<td><%$tagword.tag_name%></td>
    	  	<td><%$tagword.TAGCOUNT%></td>	  	
    	  	<td><%$tagword.tag_createdate%></td>
    	  	<td class="text-right"><%$tagword.icon_approve%><%$tagword.icon_del%><%$tagword.icon_edit%></td>
    	   	<td><% $tagword.licon %></td>
    	  	</tr>
    	  	<%/if%>
    <%/foreach%>
</table>

<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="tag-cloud-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>   
		
<% include file="mark_all_checkboxes.tpl" %>
<br><br>markierte: <select class="form-control" name="aktion">
<option value="masstagdelete">l&ouml;schen</option>
<option value="masstagapprove">genehmigen</option>
<option value="masstagdisapprove">nicht genehmigen</option>
</select>
<input type="submit" class="btn btn-primary" value="GO">
</form>
<%/if%>

<% if ($tagcloudobj.approved > 0) %>
<h3>Ver&ouml;ffentlichte Tags</h3>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">
<table  class="table table-striped table-hover"  id="tag-cloud-table">
	<thead><tr>
		<th></th>	
		<th><a href="<%$PHPSELF%>?epage=<%$epage%>&tagorder=TAGNAME">Wort</a></th>
		<th><a href="<%$PHPSELF%>?epage=<%$epage%>&tagorder=TAGCOUNT">Anzahl</a></th>
		<th>eingetragen am</th>
		<th>Optionen</th>
		<th></th>
	</tr></thead>
<% foreach from=$tagcloudobj.words item=tagword name=gloop %>

<% if ($tagword.tag_approved==1) %>
	  <tr>
	  	<td><input class="checkall" type="checkbox" name="tagids[]" value="<%$tagword.TAGID%>"></td>
	  	<td><%$tagword.tag_name%></td>
	  	<td><%$tagword.TAGCOUNT%></td>	  	
	  	<td><%$tagword.tag_createdate%></td>
	  	<td class="text-right"><%$tagword.icon_approve%><%$tagword.icon_del%><%$tagword.icon_edit%></td>
	   	<td><% $tagword.licon %></td>
	  	</tr>
	  		  	<%/if%>
<%/foreach%>
</table>

<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="tag-cloud-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>   
		
<% include file="mark_all_checkboxes.tpl" %>
<div class="row form-inline">
    <div class="col-md-3">
    markierte: <select class="form-control" name="aktion">
<option value="masstagdelete">l&ouml;schen</option>
<option value="masstagapprove">genehmigen</option>
<option value="masstagdisapprove">nicht genehmigen</option>
</select>
<input type="submit" class="btn btn-primary" value="GO">
</form>
    </div>
</div>
<%/if%>

<% if (count($tagcloudobj.words)==0) %>
<div class="bg-info text-info">Es liegen noch keine Tags vor.</div>
<% /if %>

<% /if %>


<% if ($aktion=='edit') %>
<h3>Bearbeiten</h3>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="aktion" value="asavetag">
<input type="hidden" name="id" value="<%$singletag.TAGID%>">
<table  class="table table-striped table-hover">
	  <tr>
	  	<td>Tag:</td>
	  	<td><input type="text" class="form-control" name="FORM[tag_name]" value="<%$singletag.tag_name%>"></td>
	 </tr>
</table>
<input type="submit" class="btn btn-primary" value="{LA_SAVE}">
</form>
<h3>Relationen</h3>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="id" value="<%$singletag.TAGID%>">
<table  class="table table-striped table-hover">
	  <tr><td valign="top">
<% assign var=line_break value="10" %>	  
<% foreach from=$singletag_relation item=reltag name=reloop%>
	   <input type="checkbox" name="REL[]" value="<%$reltag.RELID%>"><%$reltag.TTITLE%><br>
	 <% if ($smarty.foreach.reloop.iteration % $line_break == 0)%></td><td valign="top"><%/if%> 
<%/foreach%>
			</td>
	 </tr>
</table>

<div class="row form-inline">
    <div class="col-md-3">
markierte: <select class="form-control" name="aktion">
<option value="delrelation">{LA_DELETE}</option>
</select>
<input type="submit" class="btn btn-primary" value="GO">
</form>
    </div>
</div>

<% /if %>

<% if ($cmd=='conf') %>
<h3>Konfiguration</h3>
<%$TAGCLOUD.conf%>
<form action="<%$PHPSELF%>" method="post" class="form jsonform">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="set_perma_link">

<div class="form-group">
    <label>Permalink Bezeichnung</label>
    <input type="text" class="form-control" name="" value="<%$gbl_config.tagcloud_perma_link%>" disabled="">
</div>
<%$subbtn%>
</form>
<% /if %>