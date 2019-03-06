  <table class="tab_std" >
        <tr class="trheader">
        <td>Mitarbeiter</td>
        <td>Zeit von</td>
        <td>Zeit bis</td>
        <td>Arbeitszeit</td>
        </tr>
         <% foreach from=$N_OBJ.DAY.employees item=employee name=mt %>
                 <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
          <tr class="<%$sclass%>">
          <td>
          <%$employee.mitarbeiter_name%>:
          </td><td>
          <%$employee.dt_fromtime.time.formatedtime %>
          </td><td><%$employee.dt_totime.time.formatedtime %>
          </td><td><%$employee.dt_duration%> Std.
          </td></tr>
         <%/foreach%>                   
         </table>
