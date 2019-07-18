<h3>{LBL_CALGROUPS}</h3>
<div class="btn-group">
    <a class="btn btn-secondary" href="javascript:void(0);" title="neu anlegen" data-toggle="modal" data-target="#addgroupop">Neuer Kalender</a>    
</div>
   <form action="<%$PHPSELF%>" class="form jsonform" method="post">
    <input type="hidden" name="epage" value="<% $epage %>">	
	<input type="hidden" name="cmd" value="save_group_table">
   <table class="table table-hover">
   <thead>
        <th>Gruppe</th>
        <th></th>
   </thead>
       <tbody>
           <% foreach from=$EVENT.groups item=row %>
            <tr>
                <td><input class="form-control" type="text" name="FORM[<%$row.id%>][groupname]" value="<%$row.groupname|sthsc%>"></td>
                <td><%$row.icon_edit%><%$row.icon_del%></td>
            </tr>
           <%/foreach%>
       </tbody>
   </tbody>
   </table>
   <%$subbtn%>
   </form>

<div class="modal fade" id="addgroupop" tabindex="-1" role="dialog" aria-labelledby="addgroupopLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="POST" class="form">
        <input type="hidden" name="cmd" value="add_group">
        <input type="hidden" name="epage" value="<%$epage%>">
      <div class="modal-header">
        <h5 class="modal-title" id="addgroupopLabel">Eintrag hinzufügen</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
           <div class="form-group"> 
            <label>Kalender Name</label>
            <input type="text" class="form-control" required name="FORM[groupname]" value="" placeholder="Name">
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