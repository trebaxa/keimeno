<h3>{LBL_OTPROGRAMS}</h3>
<div class="btn-group">
    <a href="#" data-toggle="modal" data-target="#newprogotimer" class="btn btn-default">Neues Programm</a>
</div>
<% if (count($OTIMER.programs)>0) %>
<form action="<%$PHPSELF%>" method="post" class="form jsonform">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="save_pro_table">
<table class="table table-striped table-hover" id="ot-pro-table" >
	  	<thead><tr>
	  	<th>{LBL_OTPROGRAMS}</th>
	  	<th>{LBL_EMPLOYEE}</th>
	  	<th>{LBL_DURATION}</th>
	  	<th>{LBL_DESCRIPTION}</th>
	  	<th class="col-md-1"></th>
	  	</tr></thead>
        <tbody>
		 <% foreach from=$OTIMER.programs item=row %>		  
		  <tr>
	  	    <td><input type="text" class="form-control" name="FORM[<%$row.id%>][pr_admintitle]" value="<%$row.pr_admintitle|sthsc%>"></td>
	  	    <td><% $row.pr_employees_names %></td>	  	  
	  	    <td><%$row.pr_duration%></td>
	  	    <td><%$row.pr_description%></td>	  	  
	  	    <td class="text-right">
              <div class="btn-group">
              <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></td>
              </div>
          </tr>
		 <%/foreach%>	
		</tbody> 	 	  		
	  	 </table>
         <%$subbtn%>
         </form>
       
<%else%>	  	 
<div class="bg-info text-info">
    Es wurden noch keine Programme gefunden.
</div>
<%/if%>

<!-- Modal -->
<div class="modal fade" id="newprogotimer" tabindex="-1" role="dialog" aria-labelledby="newprogotimerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post" class="form">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="add_program">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="newprogotimerLabel">Neues Programm</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Programm Name</label>            
            <input type="textg" class="form-control" name="FORM[pr_admintitle]">
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>