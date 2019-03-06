<% if (count($OTDATE_OBJ.DAY.employees))%>
  <section>
    <h2>Arbeitszeiten pro Tag</h2>
    <table class="table table-hover table-striped" >
      <thead>
            <tr >
              <th>Mitarbeiter</th>
              <th>Zeit von</th>
              <th>Zeit bis</th>
              <th>Arbeitszeit</th>
              </tr>
        </thead>  
        <tbody>
             <% foreach from=$OTDATE_OBJ.DAY.employees item=employee name=mt %>
              <tr>
                <td><%$employee.mitarbeiter_name%>:</td>
                <td><%$employee.dt_fromtime.time.formatedtime %></td>
                <td><%$employee.dt_totime.time.formatedtime %></td>
                <td><%$employee.dt_duration%> Std.</td>
              </tr>
             <%/foreach%>                   
        </tbody>     
      </table>
  </section>
<%/if%>