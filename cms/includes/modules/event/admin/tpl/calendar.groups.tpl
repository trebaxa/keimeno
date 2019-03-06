<h3>{LBL_CALGROUPS}</h3>
<div class="btn-group">
    <a class="btn btn-default" href="javascript:void(0);" title="neu anlegen" data-toggle="modal" data-target="#addgroupop">Neuer Kalender</a>    
</div>
   <form action="<%$PHPSELF%>" class="form jsonform" method="post">
    <input type="hidden" name="epage" value="<% $epage %>">	
	<input type="hidden" name="cmd" value="save_group_table">
   <table class="table table-hover">
   <thead>
        <th>Gruppe</th>
        <th></th>
   </thead>
   <% foreach from=$EVENT.groups item=row %>
    <tr>
        <td><input type="text" name="FORM[<%$row.id%>][groupname]" value="<%$row.groupname|sthsc%>"></td>
        <td><%$row.icon_edit%><%$row.icon_del%></td>
    </tr>
   <%/foreach%>
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
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="addgroupopLabel">Eintrag hinzufügen</h4>
      </div>
      <div class="modal-body">
           <div class="form-group"> 
            <label>Kalender Name</label>
            <input type="text" class="form-control" required name="FORM[groupname]" value="" placeholder="Name">
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