<%include file="cb.panel.header.tpl" title="Arbeitszeiten für den <b>$seldate_ger</b>"%> 
<form action="<% $PHPSELF %>" method="post" class="jsonform">
	<input type="hidden" name="cmd" value="a_daysave">
	<input type="hidden" name="refaktion" value="<%$aktion%>">
	<input type="hidden" name="id" value="<%$DAY.id%>">
	<input type="hidden" name="FORM[day_date]" value="<%$seldate_us%>">

<div class="row">
 <div class="col-md-12">
  <div class="checkbox">
        <label>
            <input type="checkbox" name="FORM[day_closed]" <% if ($DAY.day_closed==1) %>checked="checked"<%/if%> value="1">
            Keine weiteren Reservierungen f&uuml;r diesen Tag annehmen
        </label>
    </div>
 <table class="table table-striped table-hover" >
    	  	<thead><tr>
    	  	<th class="col-md-1">Mitarbeiter</th>
    	  	<th class="col-md-3">Zeit von</th>
    	  	<th class="col-md-3">Zeit bis</th>
    	  	<th>Arbeitszeit</th>
            <%*<th>Programm</th>*%>
    	  	<th class="col-md-2">Status</th>
    	  	<th class="col-md-2"></th>
    	  	</tr></thead>
    	  	 <% foreach from=$DAY.employees item=employee name=mt %>
	  	  <tr class="<% if ($employee.approval==0) %> danger<%/if%><% if ($employee.approval==1 && $employee.programs!='' && $employee.dt_duration>0) %> success<%/if%>">
	  	  <td><%$employee.mitarbeiter_name%></td>
            <td class="col-md-1">
             <div class="input-group">
	  	        <input type="text" class="form-control input-sm" name="WORK[<%$employee.id%>][dt_from]" value="<%$employee.dt_from%>">
                <div class="input-group-addon"> Uhr</div>
             </div>   
	  	  </td>
            <td class="col-md-1">
             <div class="input-group">
	  	        <input type="text" class="form-control input-sm" name="WORK[<%$employee.id%>][dt_to]" value="<%$employee.dt_to%>">
                <div class="input-group-addon"> Uhr</div>
             </div>   
	  	  </td>          
          
	  	  <td>
      	      <% if ($employee.dt_duration>0) %>        
       	        <span class="text-success"> <%$employee.dt_duration%> Std.</span>
       	        <% else %>
       	             <span class="text-danger">-</span>
       	      <%/if%>	  	   	     
    	  	  <input type="hidden" name="WORK[<%$employee.id%>][id]" value="<%$employee.wid%>">
    	  	  <input type="hidden" name="WORK[<%$employee.id%>][dt_mid]" value="<%$employee.id%>">
    	  	  <input type="hidden" name="WORK[<%$employee.id%>][dt_dayid]" value="<%$employee.dt_dayid%>">
	  	  </td>
          <%*
            <td>
       
	  	   	      
                        <% if ($employee.programs=='') %> 
	  	   	        <span class="text-danger">-</span>
                   <%else%>
                   z.B.: <%$employee.programs%> 
	  	   	       <%/if%>
             
	  	   	       </td>
                         *%>
            <td>   
                   <% if ($employee.approval==0) %>
                <p class="text-danger">Mitarbeiter DEAKTIV</p>
                <%else%>
             
	  	   	      <% if ($employee.programs=='') %>
	  	   	        <span class="text-danger">Keine Programme.</span>
	  	   	       <%/if%> 
		          <% if ($employee.dt_duration==0) %>        
	  	   	        <span class="text-danger">Nicht anwesend.</span>
	  	   	      <%/if%>	  	   	       
	  	   	      <% if ($employee.programs!='' && $employee.dt_duration>0) %>        
	  	   	        <span class="text-success">arbeitet</span>
	  	   	      <%/if%>
                  <%/if%>
  	   	      </td>
  	   	      <td class="text-right">
                <div class="btn-group">
                       <a title="Arbeitszeit wiederholen lassen" class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=repeatworktime&mid=<%$employee.id%>&dayid=<%$employee.dt_dayid%>"><i class="far fa-file-alt"></i></a>
                       <a class="btn btn-secondary" title="Programme ansehen" href="run.php?epage=<%$epage%>&aktion=otprograms&mid=<%$employee.id%>"><i class="fa fa-eye"></i></a>
                    </div>   
                  </td>                       
	  	  </tr>
	  	 <%/foreach%>	  	 
	  	 </table>
 </div>

</div>	
    
    
    
	  	
	  	<input type="hidden" name="epage" value="<%$epage%>">
          <%$subbtn%>
          </form>
<%include file="cb.panel.footer.tpl" %> 


