    <table class="table table-striped table-hover" >
<% foreach from=$CUSTGROUPS.customers item=row %>
    <tr>
	 <td><%$row.kid%></td>
	 <td><%$row.nachname%></td>
	 <td><%$row.vorname%> </td>
	 <td><%$row.firma%></td>
	 <td><%$row.email%></td>
	 <td><%$row.email_notpublic%></td>
	 <td class="text-right"><% foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%></td>
     </tr>
   <%/foreach%>
   </table>