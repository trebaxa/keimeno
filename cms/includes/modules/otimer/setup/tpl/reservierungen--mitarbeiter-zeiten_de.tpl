<% if ($otimer.employee.id) > 0 %>
<h1>Angebote und Verfügbarkeit von &quot;<%$otimer.employee.mitarbeiter_name%>&quot;</h1>
<br>
<strong><%$otimer.employee.mitarbeiter_name%></strong> führt folgende Programme 
aus:<br> <i><%$otimer.employee.programs%></i><br>
<br>
<table class="tab_std" >
    <tr class="trheader">
        <td>Datum</td>
        <td>von</td>
        <td>bis</td>
        <td>Angebot</td>
    </tr>
    <% foreach from=$otimer.employee.workingdays item=day name=dmt %> 
     <% foreach from=$day.DAY.employees item=employee name=mt %>
    <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %> <% assign var=sclass value="row1" %>
    <% /if %> 
    <% if ($otimer.employee.id==$employee.id && $employee.dt_duration > 0 && $employee.dt_fromtime.timeint>=$employee.dt_today.timeint) %>
    <tr class="<%$sclass%>">
        <td><% $day.date.date_ger %></td>
        <td><%$employee.dt_fromtime.time.formatedtime %> </td>
        <td><%$employee.dt_totime.time.formatedtime %> </td>
        <td>
        <form role="form" method="POST" action="<%$PATH_CMS%>index.php?page=<%$page%>&aktion=addnew&employeeid=<%$employee.id%>&seldate=<%$day.date.date_us%>&hour=<%$employee.dt_fromtime.time.H%>">
        <select class="form-control" name="id">
           <% foreach from=$employee.programlist item=programm name=lmt %>
           <option value="<%$programm.PROGID%>"> <%$programm.pr_title%></option>
           <%/foreach%>
        </select>

<% html_subbtn class="btn btn-primary" value="{LBL_OTBUCHEN}" %>
        </form>
      
        </td>
    </tr>
    <%/if%> <%/foreach%> <%/foreach%>
</table>
<%else%>
 <div class="infobox">Dieser Betreuer existiert nicht mehr.</div>
<%/if%>
