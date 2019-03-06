<% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %>
<form role="form" action="<% $PHPSELF %>" method="post">
    <input type="hidden" name="otaktion" value="a_save">
    <input type="hidden" name="aktion" value="addnew">
    <input type="hidden" name="refaktion" value="<%$aktion%>">
    <input type="hidden" name="FORM[ndate]" value="<%$otimer.seldate%>">
    <input type="hidden" name="page" value="<%$otimer.page%>">
    <input type="hidden" name="FORM[prog_id]" value="<%$PROG.PROGID%>">
    <input type="hidden" name="id" value="<%$PROG.PROGID%>">
    <div class="form-group">
      <label>Ihre Programm Auswahl:</label>
      <select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
        <% $jav_prog_select_allowed%>
      </select>
    </div>
    <div class="form-group">
      <label>Beschreibung:</label>
      <% $PROG.pr_description%>
    </div>  
    <div class="form-group">
      <label>Folgende Mitarbeiter sind heute im Einsatz:</label>
      <table class="table table-striped table-hover" >
      <thead>
          <tr>
            <th>Mitarbeiter</th>
            <th>von</th>
            <th>bis</th>
            <th>Angebot</th>
            <th>Verfügbarkeit</th>
          </tr>
        </thead>
         <% foreach from=$OTDATE_OBJ.DAY.employees item=employee name=mt %>
         <% if ($employee.dt_duration > 0) %>
          <tr>
            <td>
            <%$employee.mitarbeiter_name%>
            </td><td>
            <%$employee.dt_fromtime.time.formatedtime %>
            </td><td><%$employee.dt_totime.time.formatedtime%>
            </td>
            <td><%$employee.programs%></td>
            <td><a class="btn btn-default" href="<%$PATH_CMS%>index.php?page=<%$page%>&aktion=showemploytab&employid=<%$employee.id%>">Verfügbarkeit</a></td>
          </tr>
          <%/if%>
         <%/foreach%>                   
         </table>
    </div>   
<div class="form-group">
  <label>Ihre Betreuer Auswahl:</label>
  <select class="form-control" id="employeeid" name="employeeid" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,min.options[min.selectedIndex].value,this.options[this.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
   <% foreach from=$OTDATE_OBJ.DAY.employees item=employee name=mt %>
   <% if ($employee.dt_duration>0) %>
     <option <% if ($employee.MID==$OTFORM.employeeid) %>selected <%/if%>value="<%$employee.MID%>">
        <%$employee.dt_fromtime.time.formatedtime%>-<%$employee.dt_totime.time.formatedtime%> <%$employee.mitarbeiter_name%>
     </option>
     <% /if %>
   <%/foreach%>
   </select>
</div>   
  <div class="row">
  <div class="form-group col-md-6">
    <label>Wann möchten Sie buchen?</label> 
    <select class="form-control" id="hour" name="hour" onChange="doRequestFromValue(this.options[this.selectedIndex].value,min.options[min.selectedIndex].value,employeeid.options[employeeid.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
      <% foreach from=$OTDATE_OBJ.hours_day.hours item=hour name=mt %>
       <option <% if ($hour==$OTDATE_OBJ.hours_day.selhour) %>selected <%/if%>value="<%$hour%>"><%$hour%></option>
      <%/foreach%>        
    </select>
  </div>      
  <div class="form-group col-md-6">
      <label>&nbsp;</label>
        <select class="form-control" id="min" name="min" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,this.options[this.selectedIndex].value,employeeid.options[employeeid.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
         <% foreach from=$OTDATE_OBJ.min_day item=min name=mt %>
           <option <% if ($min==$OTDATE_OBJ.hours_day.selmin) %>selected <%/if%>value="<%$min%>"><%$min%></option>
         <%/foreach%>        
        </select>
  </div>  
  </div>       
  <div class="well" id="timeto"></div>
  <div class="form-group">  
        <label>Kunde Kommentar:</label>
        <textarea class="form-control"  rows="6" cols="60" name="FORM[comments_cu]"><%$OTFORM.comments_cu%></textarea>       
  </div>
 <div class="row">
   <div class="col-md-6"><a class="btn btn-default" href="<% $PATH_CMS %>index.php?page=540">{LBL_BACK}</a></div>
   <div class="col-md-6 text-right"><button type="submit" class="btn btn-primary">weiter</button></div>
 </div>
 </form>
<script>
//<![CDATA[
  var ho = document.getElementById('hour');
  var mino = document.getElementById('min');
  var emp= document.getElementById('employeeid');
  doRequestFromValue(ho.options[ho.selectedIndex].value,mino.options[mino.selectedIndex].value,emp.options[emp.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif');
  $('#hour').trigger('change');
//]]>
</script>
<%else%>
  <div class="alert-alert-info">Das Datum liegt in der Vergangenheit. Reservierungen könnne nicht durchgeführt werden.</div>
<%/if%>