<% if (count($FLEXTEMP.flextpl.groups)>0)%>
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_group_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />     
      <table class="table table-striped table-hover" >
            <thead>
                <tr>
                    <th>Name</th>                    
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$FLEXTEMP.flextpl.groups item=row %>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.g_name|sthsc%>" name="FORM[<%$row.id%>][g_name]" />
                    <input type="hidden" value="<% $row.g_ident|sthsc%>" name="FORM[<%$row.id%>][g_ident]" />
                    </td>
                    <td class="text-right">                    
                     <div class="btn-group">                     
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
        </table>
         <%$subbtn%>
        </form>
<%else%>
    <div class="alert alert-info">Keine Gruppen angelegt</div>
<%/if%>  