<%include file="cb.panel.header.tpl" title="Email-Listen verwalten"%>
<div class="row">
<div class="col-md-6">


	<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<% $NEWSLETTER.FORM.id%>">
		<input type="hidden" name="aktion" value="save_group">
        <input type="hidden" name="epage" value="<%$epage%>">
	       <label>Newsletter Gruppe Name:</label>
    	   <div class="input-group">
            <input name="FORM[group_name]" required="" type="text" class="form-control" value="<%$NEWSLETTER.FORM.group_name|htmlspecialchars %>"/>
            <div class="input-group-btn"><%$subbtn%></div>
           </div>		
</form>

		</div>
        <div class="col-md-6">
            <div class="alert alert-info">Erstellen Sie hier verschiedene Kunden-Empfänger-Gruppen. Somit ist es möglich einen Newsletter nur an bestimmte Kunden zu senden.</div>
        </div>
</div>        
		
<% if (count($NEWSLETTER.ngroups)>0) %>
<h3>Newsletter-Gruppen:</h3>
<table class="table table-striped table-hover" id="lists-table">
<thead><tr>
		<th>Gruppe</th>
		<th>Gruppierte Kunden</th>
		<th>pure Emails</th>
		<th>Optionen</th>
		</tr></thead>
<% foreach from=$NEWSLETTER.ngroups item=row %>		
  	<tr>
  		<td><% $row.group_name%></td>
  		<td class="text-center"><%$row.KCOUNT%></td>
  		<td class="text-center"><%$row.ECOUNT%></td>
  		<td class="text-right">
        <div class="btn-group">
		<% foreach from=$row.icons item=icon %>		 <%$icon%>		<%/foreach%>
        </div>	
		</td>
  	</tr>
  <%/foreach%>
</table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="lists-table" scope="global"%>
<%include file="table.sorting.script.tpl"%> 		
<%/if%>

 <%include file="cb.panel.footer.tpl"%> 