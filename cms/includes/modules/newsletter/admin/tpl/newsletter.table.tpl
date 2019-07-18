<%include file="cb.panel.header.tpl" title="{LBLA_NEWHISTORY}"%>
<table class="table table-striped table-hover" id="newsletter-table-">
<thead><tr>
	<th>Betreff</th>
	<th>Empf&auml;nger</th>
	<th>gesendet am</th>
	<th></th>
</tr></thead>
<tbody>
<% foreach from=$NEWSLETTER.newsletter_table item=row %>	
  <tr>
        <td><% $row.e_subject %></td>
        <td><% $row.e_sendcount %></td>
        <td><%$row.e_date%> um <% $row.e_time %></td>
        <td class="text-right">
            <div class="btn-group">
                <a class="btn btn-secondary" href="<%$eurl%>cmd=preview&id=<%$row.id%>" title="Versenden"><i class="fa fa-envelope"></i></a>
                <% foreach from=$row.icons item=icon %><%$icon%><%/foreach%>
                <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&id=<% $row.id %>&cmd=a_tracking" title="Tracking anzeigen"><i class="far fa-chart-bar"><!----></i></a>  	     
                <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=clone&id=<% $row.id %>" title="Klonen"><i class="far fa-file-alt"><!----></i></a>
                <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&id=<%$row.id%>&cmd=xlsexport"><i class="far fa-file-alt-excel"><!----></i></a>
            </div>   
  	     </td>
   </tr>
   <%/foreach%>
   </tbody>
  </table>
  
   <%include file="cb.panel.footer.tpl"%> 