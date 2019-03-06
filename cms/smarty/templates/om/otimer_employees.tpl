<% if (count($OTDATE_OBJ.DAY.employees_year)>0) %>
<br>
<h3>Folgende Betreuer stehen Ihnen zur VerfÃ¼gung:</h3> <br>
<table class="tab_std"  width="100%">
    <% foreach from=$OTDATE_OBJ.DAY.employees_year item=employee name=mt %> 
    <% if ($employee.dt_duration > 0) %>
    <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %> <% assign var=sclass value="row1" %>    <% /if %>
    <tr class="<%$sclass%>">
        <td><%$employee.mitarbeiter_name%> </td>
        <td style="text-align:right">
        <a href="<%$PATH_CMS%>index.php?page=<%$page%>&aktion=showemploytab&employid=<%$employee.id%>">
        VerfÃ¼gbarkeit anzeigen</a></td>
    </tr>
    <%/if%> <%/foreach%>
</table>
<%/if%>
