<% if ($aktion=='worktimem') %>
 	<%include file="cb.panel.header.tpl" title="Arbeitszeiten"%>
<%/if%> 	

<% if ($aktion=='worktime') %>
	<%include file="cb.panel.header.tpl" title="Arbeitszeiten vom <%$seldate%>"%>
<%/if%>

<div class="btn-group mb-lg">
    <a class="btn btn-primary" href="<%$eurl%>cmd=dayoptions&seldate=<%$OTIMER.selecteddate%>"><i class="far fa-edit"></i> Arbeitszeiten festlegen</a>
</div>


<% if (count($wt_table)>0) %>
<table class="table table-striped table-hover" id="ot-wt-table" >
	  	<thead><tr>
    	  	<th class="col-md-1">Mitarbeiter</th>
    	  	<th>am</th>
    	  	<th class="col-md-1">Zeit von</th>
    	  	<th class="col-md-1">Zeit bis</th>
    	  	<th>Arbeitszeit</th>
    	  	<th></th>
	  	</tr></thead>
        <tbody>
		 <% foreach from=$wt_table item=employee name=mt %>
		  <% if ($employee.duration > 0) %>
		  <tr <% if ($dateger!="" && $dateger!=$employee.dt_fromtime.date_ger) %> class="singlelinetop"<%/if%>>
		  	<% assign var=dateger value=$employee.dt_fromtime.date_ger %>
	  	  <td>	  	  <%$employee.mitarbeiter_name%>:	  	  </td>
	  	  <td>	  	  <% $employee.dt_fromtime.date_ger %>	  	  </td>	  	  
	  	  <td>	  	  <%$employee.dt_from%>	  	  </td>
	  	  <td><%$employee.dt_to%>	  	  </td>
	  	  <td class="text-right"><%$employee.dt_duration%> Std.	  	  </td>
	  	  <td class="text-right">
                        <div class="btn-group">
                <a title="Arbeitszeit wiederholen lassen" class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=repeatworktime&mid=<%$employee.id%>&dayid=<%$employee.dt_dayid%>"><i class="far fa-file-alt"></i></a>
                <%$employee.icon_del%>
            </div>
            </td>
          </tr>
			<% /if %>
				
		 <%/foreach%>	
		</tbody> 	 	  		
	  	 </table>
      
<%else%>	  	 
    <div class="alert-info alert">
        Es wurden noch keine Arbeitszeiten für diesen Tag zugeteilt.
    </div>
<%/if%>
<%include file="cb.panel.footer.tpl" %>         