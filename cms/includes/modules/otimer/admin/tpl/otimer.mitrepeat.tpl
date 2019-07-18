<%include file="cb.panel.header.tpl" title="Wiederholung definieren"%> 
    <form action="<% $PHPSELF %>" method="post">
        <input type="hidden" name="dayid" value="<%$DAY.id%>">
        <input type="hidden" name="mid" value="<%$EMPLOYEE.id%>">
    	<input type="hidden" name="cmd" value="repeatmit">
    	<input type="hidden" name="epage" value="<%$epage%>">
    	
    	<div class="form-group">
    	  	<label>Mitarbeiter:</label></td>	  	 
    	  	 <p><%$EMPLOYEE.mi_lastname%>, <%$EMPLOYEE.mi_firstname%></p>           
    	</div>			
    	<div class="form-group">
    	  	<label>Beginn:</label>
    	  	<p><%$REPEAT.weekday%>, <%$DAY.ger_date%>, <%$DAYMID.dt_from%> - <%$DAYMID.dt_to%> Uhr</p>
    	</div>			
    		
        <div class="form-group">
    		<label>Wiederholung:</label>
    		 <select class="form-control custom-select" name="repdays">		 
        		 <option value="1">jeden Tag</option>
        		 <option value="7">jede Woche</option>		 
        		 <option value="14">alle 2 Wochen</option>		 
        		 <option value="21">alle 3 Wochen</option>		 
        		 <option value="28">alle 4 Wochen</option>		 
    		</select>
    	</div>
    
        <div class="form-group">    
    		<label>Anzahl Wiederholungen:</label>
    		<select class="form-control custom-select" name="timespan">		 
    		 <% $REPEAT.timespan %>
    		</select>		
    	</div>
    <%$subbtn%>
    </form>
<%include file="cb.panel.footer.tpl"%>