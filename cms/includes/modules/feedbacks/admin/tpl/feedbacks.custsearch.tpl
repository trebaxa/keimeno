<table class="table table-striped table-hover">
<% foreach from=$FEEDB.customers item=row %>
<tr>
    <td><%$row.nachname%></td>
    <td><a href="javascript:void(0)" onclick="add_customer_to_feedback(<%$row.kid%>,'<%$row.nachname%> <%$row.vorname%>')">auswählen</a></td>
</tr>                
<%/foreach%>
</table>