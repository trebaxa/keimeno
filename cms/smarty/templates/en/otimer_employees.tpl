<% if (count($OTDATE_OBJ.DAY.employees_year)>0) %>

<h3>Folgende Betreuer stehen Ihnen zur Verfügung:</h3> <br>
<table class="table table-hover table-striped">
    <% foreach from=$OTDATE_OBJ.DAY.employees_year item=employee name=mt %> 
      <% if ($employee.dt_duration > 0) %>
        <tr>
            <td><%$employee.mitarbeiter_name%> </td>
            <td class="text-right">
              <a class="btn btn-default" href="<%$PATH_CMS%>index.php?page=<%$page%>&aktion=showemploytab&employid=<%$employee.id%>">Verfügbarkeit anzeigen</a>
            </td>
        </tr>
      <%/if%>
    <%/foreach%>
</table>
<%/if%>