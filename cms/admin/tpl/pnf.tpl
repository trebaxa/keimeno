<%include file="cb.panel.header.tpl" icon="fa-database" title="404 Weiterleitungen"%>

<% if (count($PNF.ptable)>0) %> 
Einträge: <span class="badge"><%$PNF.ptable_count%></span>   
<a href="<%$eurl%>cmd=clear" class="btn btn-default json-link">Tabelle leeren</a>       	
<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
<table class="table table-striped table-hover" >
			<thead>
                <tr>
        			<th>Time</th>
        			<th>Page ID</th>
        			<th>URL</th>
        			<th>Redirect To</th>
                    <th class="text-right">Aufrufe</th>
                    <th>Agent</th>
                    <th></th>
    			</tr>
            </thead>
 		<% foreach from=$PNF.ptable item=row %>         
 		  <tr>
    		   <td><%$row.pnf_time_ger%></td>
               <td><%$row.pnf_page%></td>
               <td><a href="<%$row.pnf_uri%>" target="_blank"><%$row.pnf_uri%></a></td>
        	   <td><input name="FORM[<%$row.pnf_hash%>][pnf_url]" type="text" class="form-control" value="<%$row.pnf_url|hsc%>"></td>
               <td class="text-right"><%$row.pnf_calls%></td>
               <td><%$row.pnf_user%></td>
               <td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
         </tr>
 		<%/foreach%>
</table>
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="pnfsave">
<%$subbtn%>
</form>
<%include file="cb.panel.footer.tpl"%> 
<%else%>
<div class="bg-success">Prima! Es wurden bisher noch alle Seiten gefunden.</div>
<%/if%>

<script>
function relaod_pnf() {
    simple_load('admincontent','<%$eurl%>');
}
</script>