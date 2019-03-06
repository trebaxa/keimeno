<div class="btn-group">
<a class="btn btn-default" href="run.php?epage=<%$epage%>&aktion=addnewdate&seldate=<%$seldate_us%>">{LBL_ADD_OTDATE} <% $seldate %> {LBL_CREATE}</a>  <a class="btn btn-default" href="run.php?epage=<%$epage%>&aktion=repeatappointment&dateid=<%$dateid%>">Diesen Termin wiederholt eintragen lassen</a>
</div>

<% if ($aktion=='editdate')%>
<div class="page-header"><h1>{LBL_EDIT_APPOINT} - <%$seldate%></h1></div>
<span style="font-size:14pt"><%$N_OBJ.starttime.time.H%></span><sup style="font-size:80%"><%$N_OBJ.starttime.time.i%></sup> Uhr,
 <% $PROG.pr_admintitle%>
</span><hr>
<% else %>
<div class="page-header"><h1>{LBL_ADD_APPOINT} - <%$seldate%></h1></div>
<%/if%>
<%include file="cb.panel.header.tpl" title="Kunde suchen"%>
        <div class="form-group">
            <label>Stichwort:</label>
            <div class="input-group">
                <input autocomplete="off" placeholder="Suchbegriff" type="text" class="form-control" id="wort"  value="" name="wort" size="60" onKeyUp="sendRequest2InnerHTMLWithLimit('wort','ksuche_areaot','ax_searchot','run','&epage=<%$epage%>&orderby=nachname&dateid=<%$dateid%>&seldate=<%$seldate_us%>&orgaktion=<%$aktion%>&id=<%$id%>',3,'../images/opt_loader.gif')"/>
                <div class="input-group-addon"><i class="fa fa-search"></i></div>
            </div>
        </div>
        <div id="ksuche_areaot" name="ksuche_areaot"></div>
<%include file="cb.panel.footer.tpl" %>

 
<%if ($KOBJ.kid>0) %>
<div class="row">
<div class="col-md-6">


<%include file="cb.panel.header.tpl" title="Termin definieren"%> 
<form action="<% $PHPSELF %>" method="post">
	<input type="hidden" name="FORM[group_id]" value="<% $group_id%>">
	<input type="hidden" name="aktion" value="a_save">
	<input type="hidden" name="refaktion" value="<%$aktion%>">
	<input type="hidden" name="FORM[ndate]" value="<%$seldate_us%>">
	<input type="hidden" name="duration" value="<%$PROG.pr_duration%>">
	<input type="hidden" name="groupid" value="<%$group_id%>">
	<input type="hidden" name="FORM[prog_id]" value="<%$PROG.PROGID%>">
  <input type="hidden" name="dateid" value="<%$dateid%>">	
  <input type="hidden" name="epage" value="<%$epage%>">
  
		<table class="table table-striped table-hover" >
		<tr>
	  	<td valign="top" class="col-md-3">
	  	    <strong>{LBL_PROGRAM}:</strong>
        </td>
	  	<td>
          <select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
	  	    <% $jav_prog_select%>
	  	    </select>
	  	
	  		<legend>{LBL_DESCRIPTION}:</legend><% $PROG.pr_description|st%>
	  		<p><strong>{LBL_DURATION}:</strong><% $PROG.pr_duration%></p>
	  		<p><strong>Wer kann dieses Programm ausf&uuml;hren?</p>
	  	<table class="table table-striped table-hover" >
    	  	<thead><tr>
    	  	<th>Mitarbeiter</th>
    	  	<th>Zeit von</th>
    	  	<th>Zeit bis</th>
    	  	<th>Arbeitszeit</th>
    	  	</tr></thead>
            <tbody>
    		 <% foreach from=$DAY.employees item=employee name=mt %>
    		 <% if ($employee.dt_duration > 0) %>
    		  	  	 
    	  	  <tr>
    	  	  <td>
    	  	  <%$employee.mitarbeiter_name%>:
    	  	  </td><td>
    	  	  <%$employee.dt_from%>
    	  	  </td><td><%$employee.dt_to%>
    	  	  </td><td><%$employee.dt_durationstr%> Std.
    	  	  </td>
              </tr>
              <%/if%>
    		 <%/foreach%>
             </tbody>		 	  		
	  	 </table>
	  		
	  	</td>
		</tr>		
		<tr>
	  	<td><strong>Ausf&uuml;hrender {LBL_EMPLOYEE}:</strong> </td>
	  	<td><select class="form-control" id="employid" name="employid" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,min.options[min.selectedIndex].value,this.options[this.selectedIndex].value,'timeto','calcendtime','run','&epage=<%$epage%>&groupid=<%$group_id%>&dateid=<%$N_OBJ.DATEID%>&seldate=<%$seldate_us%>&duration=<%$PROG.pr_duration%>','../images/opt_loader.gif')">
	  	<% foreach from=$DAY.employees item=employee name=mt %>
	  	 <% if ($employee.dt_duration > 0) %>
	  	<option <% if ($employee.MID==$N_OBJ.prog_employeeid) %>selected <%/if%> value="<%$employee.MID%>">
	  	 <%$employee.dt_fromtime.time.formatedtime%>-<%$employee.dt_totime.time.formatedtime%> <%$employee.mitarbeiter_name%>
	  	 </option>
	  	    <%/if%>
	  			 <%/foreach%>	

	  	</select></td>
		</tr>			
		<tr>
	  	<td><strong>{LBL_CUSTOMER}:</strong> </td><td>
	  	<%if ($KOBJ.kid>0) %>
	  	<%$KOBJ.nachname%>,<%$KOBJ.vorname%> 
	  	<br><%$KOBJ.str%>
	  	<br><%$KOBJ.plz%> <%$KOBJ.ort%>
	  	<br>Tel.:<%$KOBJ.tel%>
	  	<br><a href="mailto:<%$KOBJ.email%>"><%$KOBJ.email%></a>
	  	<%else%>
	  	<span class="redimportant">
	  	 Kein Kunde zugeordnet.
	  	 </span>
	  	<%/if%>
	  	</td>
		</tr>			
		<tr>
        <td valign="top">
        <div class="row">
		  <strong>Termin findet statt um:</strong>
		</td>
        <td valign="top">
        <div class="form-group col-md-6">
            <label for="hour">Stunde</label> 
    		 <select class="form-control" id="hour" name="hour" onChange="doRequestFromValue(this.options[this.selectedIndex].value,min.options[min.selectedIndex].value,employid.options[employid.selectedIndex].value,'timeto','calcendtime','run','&epage=<%$epage%>&groupid=<%$group_id%>&dateid=<%$N_OBJ.DATEID%>&seldate=<%$seldate_us%>&duration=<%$PROG.pr_duration%>','../images/opt_loader.gif')">
    		 <% foreach from=$OTDATE_OBJ.hours_day.hours item=hour name=mt %>
    		 <option <% if ($hour==$OTDATE_OBJ.hours_day.selhour) %>selected <%/if%>value="<%$hour%>"><%$hour%></option>
    		 <%/foreach%>		 
    		</select>
            </div>
        <div class="form-group col-md-6">    
        <label>Minute</label>
            <select class="form-control" id="min" name="min" onChange="doRequestFromValue(hour.options[hour.selectedIndex].value,this.options[this.selectedIndex].value,employid.options[employid.selectedIndex].value,'timeto','calcendtime','run','&epage=<%$epage%>&groupid=<%$group_id%>&dateid=<%$N_OBJ.DATEID%>&seldate=<%$seldate_us%>&duration=<%$PROG.pr_duration%>','../images/opt_loader.gif')">
    		 <% foreach from=$OTDATE_OBJ.min_day item=min name=mt %>
    		 <option <% if ($min==$OTDATE_OBJ.hours_day.selmin) %>selected <%/if%>value="<%$min%>"><%$min%></option>
    		 <%/foreach%>		 
    		</select>
        </div>
        </div>
        <p>
            <strong>und endet am:</strong> <br>
    		<div id="timeto"></div>
           </p> 
		</td></tr>
		</table>	
         
		
		<div class="form-group">
		  <label>{LBL_COMMENT_CU}:</label>
            <textarea class="form-control se-html-" rows="6" cols="60" name="FORM[comments_cu]"><%$N_OBJ.comments_cu|hsc%></textarea>
        </div>
		<div class="form-group">
            <label>{LBL_COMMENT_MA}:</label>
            <textarea class="form-control se-html-" rows="6" cols="60" name="FORM[comments_ma]"><%$N_OBJ.comments_ma|hsc%></textarea>
        </div>    
		
        <%$subbtn%>	
        
      
  </form>
<%include file="cb.panel.footer.tpl" %> 
</div>
</div>			
<%else%>
<div class="alert alert-info">Bitte erst einen Kunden festlegen. Sollte der Kunde noch nicht erfasst sein, so kann dieser 
    <a href="kreg.php">hier</a> angelegt werden.
</div>
<%/if%>		
	  	
	  	
<script type="text/javascript">
//<![CDATA[
 var ho = document.getElementById('hour');
 var mino = document.getElementById('min');
 var emp = document.getElementById('employid');
 doRequestFromValue(ho.options[ho.selectedIndex].value,mino.options[mino.selectedIndex].value,emp.options[emp.selectedIndex].value,'timeto','calcendtime','run','&epage=<%$epage%>&groupid=<%$group_id%>&dateid=<%$N_OBJ.DATEID%>&seldate=<%$seldate_us%>&duration=<%$PROG.pr_duration%>','../images/opt_loader.gif');
//]]>
</script>	
	




<% include file="otimer.day.tpl" %>



