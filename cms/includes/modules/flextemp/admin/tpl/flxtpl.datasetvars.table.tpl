<% if (count($FLEXTEMP.flextpl.datasetvarsdb)>0)%>
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_flexvar_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />     
          <input type="hidden" value="1" name="dataset" />
          <input type="hidden" value="<%$GET.gid%>" name="gid" />
      <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Field</th>                    
                    <th>Beschreibung</th>
                    <th>Sort.</th>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Gruppe</th>
                    <th></th>
                </tr>
            </thead>
           <tbody>
            <% foreach from=$FLEXTEMP.flextpl.datasetvarsdb item=row key=column%>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_name|sthsc%>" name="FORM[<%$row.id%>][v_name]" /></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_descr|sthsc%>" name="FORM[<%$row.id%>][v_descr]" /></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_order|sthsc%>" name="FORM[<%$row.id%>][v_order]" /></td>
                    <td><% $row.v_type%></td>
                    <td><code>&lt;%$row.<%$column%>.value%&gt;</code></td>
                    <td>
                    <% if (count($FLEXTEMP.flextpl.groups)>0) %>
                        <select name="FORM[<%$row.id%>][v_gid]" class="form-control">
                            <%*<option <% if ($row.v_gid==0) %>selected<%/if%> value="0">- keine -</option>*%>
                           <% foreach from=$FLEXTEMP.flextpl.groups item=group %>
                                <option <% if ($row.v_gid==$group.id) %>selected<%/if%> value="<%$group.id%>"><%$group.g_name%></option>
                           <%/foreach%> 
                        </select>
                    <%else%>
                        -
                    <%/if%>
                    </td>
                    <td class="text-right">                    
                     <div class="btn-group">
                        <button class="btn btn-secondary" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=0&varid=<%$row.id%>&flxid=<%$GET.id%>','Variable Editor')"><i class="far fa-edit"></i></button>                     
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
            </tbody>
        </table>
         <%$subbtn%>
        </form>
<%else%>
    <div class="alert alert-info">Keine Data-Set Felder angelegt</div>
<%/if%>  