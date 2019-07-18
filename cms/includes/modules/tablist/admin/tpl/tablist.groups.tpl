<h3>Gruppe</h3>
<div class="btn-group">
    <a href="#" data-toggle="modal" data-target="#newprogotimer" class="btn btn-secondary">Neue Gruppe</a>
</div>
<% if (count($TABLIST.groups)>0) %>
<form action="<%$PHPSELF%>" method="post" class="form jsonform">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="save_group_table">
<table class="table table-striped table-hover" id="ot-group-table" >
	  	<thead><tr>
	  	<th>{LBL_CALTHEME}</th>	  	
	  	<th></th>
	  	</tr></thead>
        <tbody>
		 <% foreach from=$TABLIST.groups item=row %>		  
		  <tr>
	  	    <td><input type="text" class="form-control" name="FORM[<%$row.id%>][groupname]" value="<%$row.groupname|sthsc%>"></td>
	  	    <td width="200" class="text-right"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></td>
          </tr>
		 <%/foreach%>	
		</tbody> 	 	  		
	  	 </table>
         <%$subbtn%>
         </form>
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="ot-group-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%> 
<%else%>	  	 
<div class="alert alert-info">
    Es wurden noch keine Themen gefunden.
</div>
<%/if%>

<!-- Modal -->
<div class="modal fade" id="newprogotimer" tabindex="-1" role="dialog" aria-labelledby="newprogotimerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post" class="form">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="add_table">
      <div class="modal-header">
        <h5 class="modal-title" id="newprogotimerLabel">Neue Gruppe</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Programm Name</label>            
            <input type="text" required="" placeholder="Gruppen Name" class="form-control" name="FORM[groupname]">
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>