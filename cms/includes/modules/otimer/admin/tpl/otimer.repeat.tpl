


<form action="<% $PHPSELF %>" method="post">
<input type="hidden" name="epage" value="<%$epage%>">
<div class="page-header"><h1>Wiederholung definieren</h1></div>
<span style="font-size:14pt"><%$N_OBJ.starttime.time.H%></span><sup style="font-size:80%"><%$N_OBJ.starttime.time.i%></sup> Uhr,
 <% $PROG.pr_admintitle%>
</span><hr>
  <input type="hidden" name="dateid" value="<%$dateid%>">
	<input type="hidden" name="aktion" value="a_repeatapp">
	<input type="hidden" name="FORM[ndate]" value="<%$seldate_us%>">
	<input type="hidden" name="FORM[prog_id]" value="<%$PROG.PROGID%>">
	
		<table class="table table-striped table-hover"  width="600">
	<tr>
	  	<td  width="171">
	  	<strong>Beginn:</strong></td>
	  	<td> 
	  	 <%$N_OBJ.starttime.weekday%>, <%$N_OBJ.starttime.date_ger%>, <%$N_OBJ.starttime.time.H%>:<%$N_OBJ.starttime.time.i%> Uhr
	  	</td>
		</tr>			
		<tr>
	  	<td  width="171">
	  	<strong>{LBL_PROGRAM}:</strong></td>
	  	<td> <% $PROG.pr_admintitle%>
	  	<div class="bg-info text-info">
	  		<strong>{LBL_DESCRIPTION}:</strong><% $PROG.pr_description%>
	  		<br><strong>{LBL_DURATION}:</strong><% $PROG.pr_duration%>
	  		<br><strong>Default {LBL_EMPLOYEE}:</strong><% $PROG.mitarbeiter_name%>
	  		</div>
	  	</td>
		</tr>		
		<tr>
	  	<td><strong>{LBL_CUSTOMER}:</strong> </td><td>
	  	<%$KOBJ.nachname%>,<%$KOBJ.vorname%> 
	  	<br><%$KOBJ.str%>
	  	<br><%$KOBJ.plz%> <%$KOBJ.ort%>
	  	<br>Tel.:<%$KOBJ.tel%>
	  	<br><a href="mailto:<%$KOBJ.email%>"><%$KOBJ.email%></a>
		  	</td>
		</tr>			
		<tr><td >
		<strong>Wiederholung:</strong> </td><td>
		 <select class="form-control" name="repdays">		 
		 <option value="7">jede Woche</option>		 
		 <option value="14">alle 2 Wochen</option>		 
		 <option value="21">alle 3 Wochen</option>		 
		 <option value="28">alle 4 Wochen</option>		 
		</select>
		</td>
		</tr>
		<tr><td >
		<strong>Anzahl Wiederholungen:</strong> </td><td>
		 <select class="form-control" name="timespan">		 
		 <% $REPEAT.timespan %>
		</select>
		</td>
		</tr>		
		</table>	

<%$subbtn%>	</form>



