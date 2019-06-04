<% if (count($RESOURCE.flextpl.flexvars)>0)%>
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
                    <th>Code</th>                  
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$RESOURCE.flextpl.flexvars item=row %>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_name|sthsc%>" name="FORM[<%$row.id%>][v_name]" /></td>                    
                    <td><input required="" type="text" class="form-control" value="<% $row.v_descr|sthsc%>" name="FORM[<%$row.id%>][v_descr]" /></td>
                    <td><% $row.v_type%></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_order|sthsc%>" name="FORM[<%$row.id%>][v_order]" /></td>
                    <td><code><%$row.varname%></code></td>                                 
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