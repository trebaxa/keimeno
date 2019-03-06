<% if (count($FLEXTEMP.flextpl.flexvars)>0)%>
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_flexvar_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$GET.gid%>" name="gid" />
          
      <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>Field</th>                    
                    <th>Beschreibung</th>
                    <th>Type</th>
                    <th>Sort</th>
                    <th></th>
                    <th>Gruppe</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$FLEXTEMP.flextpl.flexvars item=row %>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_name|sthsc%>" name="FORM[<%$row.id%>][v_name]" /></td>                    
                    <td><input required="" type="text" class="form-control" value="<% $row.v_descr|sthsc%>" name="FORM[<%$row.id%>][v_descr]" /></td>
                    <td><% $row.v_type%></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_order|sthsc%>" name="FORM[<%$row.id%>][v_order]" /></td>
                    <td>
                      <% if ($row.v_type=='resrc') %>
                            <select class="form-control" name="FORM[<%$row.id%>][v_resrc_id]">
                                <% foreach from=$FLEXTEMP.resources.table item=resrc %>
                                    <option value="<%$resrc.FID%>" <% if ($resrc.FID==<%$row.v_resrc_id%>) %>selected<%/if%>><%$resrc.f_name%></option>
                                <%/foreach%>
                            </select>
                      <%else%>
                        <!-- <code><%$row.varname%></code> -->
                      <%/if%>  
                    </td>
                    <td>
                    <% if (count($FLEXTEMP.flextpl.groups)>0) %>
                        <select name="FORM[<%$row.id%>][v_gid]" class="form-control">
                            <option <% if ($row.v_gid==0) %>selected<%/if%> value="0">- keine -</option>
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
                        <button class="btn btn-default" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=1&varid=<%$row.id%>&flxid=<%$GET.id%>','Variable Editor')"><i class="fa fa-pencil-square-o"></i></button>
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
        </table>
        <%$subbtn%>
        </form>
<%else%>
    <div class="alert alert-info">Keine Felder angelegt</div>
<%/if%>  