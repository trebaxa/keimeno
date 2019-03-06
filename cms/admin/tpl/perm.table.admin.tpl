<%include file="cb.panel.header.tpl" title="{LBL_GROUP_POLICIES}: `$AGROUP.loaded_group.mgname`"%>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#js-addpolicy">Policy hinzufügen</button>
<form class="jsonform form-inline" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
	<input type="hidden" name="cmd" value="save_gpo">
	<input type="hidden" name="id" value="<% $GET.id %>">
	<input type="hidden" name="epage" value="<%$epage%>">
    <div class="row">
        <div class="col-md-6">	
<% foreach from=$permlist item=permgroup name=gloop %>	
	<table class="table table-striped table-hover">
	<thead><tr>
    		<th><% $permgroup.g_title %></th>
	        <th>erlaubt</th>
			<th>Key</th>
	</tr></thead>
<% foreach from=$permgroup.list item=perm name=gloop %>
    <% if ($perm.p_subgroup!=$tmp) %>
        <tr><td colspan="2"><span style="font-size:13px;font-weight:bold"><%$perm.p_subgroup%></span></td></tr>
    <%/if%>
    <% assign var=tmp value=$perm.p_subgroup %>
	  <tr>
		  <td width="360"><% $perm.p_title %></td>
			<td><input type="checkbox" <% if ($GET.id==1) %>disabled<%/if%> value="1" <% if ($perm.p_value==true) %>checked=ckecked<%/if%> name="FORM[<% $perm.p_name %>]"></td>			
			<td><%$perm.smarty_tag%></td>
		</tr>
<% /foreach %>
	</table>
<% /foreach %>	
    <% if ($GET.id!=1) %>
	   <div class="btn-group">
        <a href="<%$eurl%>" class="ajax-link btn btn-default">Zurück</a>
        <%$subbtn%>
       </div>
    <%else%>
    <div class="text-info">Administratoren Berechtigungen können nicht geändert werden.</div>   
    <%/if%>
    </div>
  </div>
 </form>
 <%include file="cb.panel.footer.tpl"%>
 
 <!-- Modal -->
<div class="modal fade" id="js-addpolicy" tabindex="-1" role="dialog" aria-labelledby="js-addpolicyLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
    <input type="hidden" name="cmd" value="add_policy">
    <input type="hidden" name="id" value="<% $GET.id %>">
    <input type="hidden" name="epage" value="<%$epage%>">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="js-addpolicyLabel">Policy</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Policy Title</label>    
            <input name="FORM[p_title]" type="text" class="form-control" value="" required="">
        </div>
        <div class="form-group">
            <label>Policy ID</label>    
            <input name="FORM[p_name]" type="text" class="form-control" value="" required="">
        </div>    
        <div class="form-group">
            <label>Group</label>    
            <select class="form-control" name="FORM[p_gid]">
            <% foreach from=$AGROUP.permgroups.groups item=row %>   
                <option value="<%$row.id%>"><%$row.g_title%></option>
            <%/foreach%>
            </select>
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