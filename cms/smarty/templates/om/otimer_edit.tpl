<% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %>
<form action="<% $PHPSELF %>" method="post">
    <input type="hidden" name="otaktion" value="a_save">
    <input type="hidden" name="aktion" value="addnew">
    <input type="hidden" name="refaktion" value="<%$aktion%>">
    <input type="hidden" name="FORM[ndate]" value="<%$otimer.seldate%>">
    <input type="hidden" name="page" value="<%$otimer.page%>">
    <input type="hidden" name="FORM[prog_id]" value="<%$PROG.PROGID%>">
    <input type="hidden" name="id" value="<%$PROG.PROGID%>">
        <div class="dotbox">
    <h2>Ihre Programm Auswahl: 
<select onChange="location.href=this.options[this.selectedIndex].value">
        <% $jav_prog_select_allowed%>
        </select></h2>  
  <strong>Beschreibung:</strong><br><% $PROG.pr_description%>
            <table border="0 cellpadding="3">
            <tr><td colspan="2"><strong>Folgende Mitarbeiter sind heute im Einsatz:</strong>
<br>
                <table class="tab_std" >
        <tr class="trheader">
        <td>Mitarbeiter</td>
        <td>von</td>
        <td>bis</td>
        <td>Angebot</td>
        <td>VerfÃ¼gbarkeit</td>
        </tr>
         <% foreach from=$OTDATE_OBJ.DAY.employees item=employee name=mt %>
 <% if ($employee.dt_duration > 0) %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
          <tr class="<%$sclass%>">
          <td>
          <%$employee.mitarbeiter_name%>
          </td><td>
          <%$employee.dt_fromtime.time.formatedtime %>
          </td><td><%$employee.dt_totime.time.formatedtime%>
          </td>
          <td>
       <%$employee.programs%>
          </td>
          <td><a href="<%$PATH_CMS%>index.php?page=<%$page%>&aktion=showemploytab&employid=<%$employee.id%>">VerfÃ¼gbarkeit</a></td>
          </tr>
          <%/if%>
         <%/foreach%>                   
         </table>
            
            </td></tr>
<tr>
<td><strong>Ihre Betreuer Auswahl:</strong></td>             
<td><select id="employeeid" name="employeeid" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,min.options[min.selectedIndex].value,this.options[this.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
 <% foreach from=$OTDATE_OBJ.DAY.employees item=employee name=mt %>
 <% if ($employee.dt_duration>0) %>
 <option <% if ($employee.MID==$OTFORM.employeeid) %>selected <%/if%>value="<%$employee.MID%>">
<%$employee.dt_fromtime.time.formatedtime%>-<%$employee.dt_totime.time.formatedtime%> <%$employee.mitarbeiter_name%>
 </option>
 <% /if %>
   <%/foreach%>  </select>
           </td>             
</tr>
         
            </table>
         </div>
          <div class="dotbox">       
        <strong>Wann mÃ¶chten Sie buchen?</strong> 
         <select id="hour" name="hour" onChange="doRequestFromValue(this.options[this.selectedIndex].value,min.options[min.selectedIndex].value,employeeid.options[employeeid.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
         <% foreach from=$OTDATE_OBJ.hours_day.hours item=hour name=mt %>
         <option <% if ($hour==$OTDATE_OBJ.hours_day.selhour) %>selected <%/if%>value="<%$hour%>"><%$hour%></option>
         <%/foreach%>        
        </select>
        
        <select id="min" name="min" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,this.options[this.selectedIndex].value,employeeid.options[employeeid.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif')">
         <% foreach from=$OTDATE_OBJ.min_day item=min name=mt %>
         <option <% if ($min==$OTDATE_OBJ.hours_day.selmin) %>selected <%/if%>value="<%$min%>"><%$min%></option>
         <%/foreach%>        
        </select> - <span id="timeto"></span>
     </div>  
  
            <div class="dotbox">  
        <strong>Kunde Kommentar:</strong><br>
        <textarea rows="6" cols="60" name="FORM[comments_cu]"><%$OTFORM.comments_cu%></textarea>       
        </div>
 <div style="float:left;margin-bottom:10px;"><a href="<% $PATH_CMS %>index.php?page=540"><% html_subbtn class="sub_btn" value="{LBL_BACK}" %></a></div>
 <div style="float:right"><% html_subbtn class="sub_btn" value="{LBL_NEXT}" %></div>
 </form>
    <script type="text/javascript">
//<![CDATA[
 var ho = document.getElementById('hour');
 var mino = document.getElementById('min');
 var emp= document.getElementById('employeeid');
 doRequestFromValue(ho.options[ho.selectedIndex].value,mino.options[mino.selectedIndex].value,emp.options[emp.selectedIndex].value,'timeto','calcendtime','index','&groupid=<%$group_id%>&page=<%$otimer.page%>&seldate=<%$otimer.seldate%>&duration=<%$PROG.pr_duration%>&id=<%$PROG.PROGID%>','.<% $PATH_CMS %>images/opt_loader.gif');
//]]>
</script>
<%else%>
<div class="infobox">Das Datum liegt in der Vergangenheit. Reservierungen kÃ¶nnne nicht durchgefÃ¼hrt werden.</div>
<br>
<%/if%>
