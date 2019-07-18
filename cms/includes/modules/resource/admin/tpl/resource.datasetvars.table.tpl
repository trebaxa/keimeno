    <div class="btn-group">
        <a class="btn btn-secondary" href="#" onclick="reload_dataset_vars('<%$GET.table%>');"><i class="fa fa-table"></i> Alle anzeigen</a>
        <button class="btn btn-secondary" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=0&varid=0&flxid=<%$GET.id%>&table=<%$GET.table%>','Variable Editor')"><i class="fa fa-plus"></i> Neues Feld</button>
        <a href="#" data-url="<%$eurl%>cmd=save_table_name" data-formname="f_table" data-id="<%$GET.table%>" class="js-clickedit btn btn-secondary"><%$RESOURCE.table.f_name%></a>
        <% if ($RESOURCE.flextpl.f_table!=$GET.table) %>
            <button class="btn btn-danger" onclick="del_table('<%$GET.table%>')" type="button"><i class="fa fa-times"></i></button>
        <%/if%>
        
    </div>
    
    
<% if (count($RESOURCE.flextpl.datasetvarsdb)>0)%>
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_flexvar_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />     
          <input type="hidden" value="1" name="dataset" />
          <input type="hidden" value="<%$GET.gid%>" name="gid" />
          <input type="hidden" value="<%$GET.table%>" name="table" />
      <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Feldname</th>                    
                    <th>Beschreibung</th>
                    <th>Sort.</th>
                    <th>Type</th>
                    <th>Code</th>                    
                    <th></th>
                </tr>
            </thead>
           
            <% foreach from=$RESOURCE.flextpl.datasetvarsdb item=row key=column%>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_name|sthsc%>" name="FORM[<%$row.id%>][v_name]" /></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_descr|sthsc%>" name="FORM[<%$row.id%>][v_descr]" /></td>
                    <td><input required="" type="text" class="form-control" value="<% $row.v_order|sthsc%>" name="FORM[<%$row.id%>][v_order]" /></td>
                    <td><% $row.v_type%></td>
                    <td><code>&lt;%$row.<%$column%>.value%&gt;</code></td>                    
                    <td class="text-right">                    
                     <div class="btn-group">
                        <button class="btn btn-secondary" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=0&varid=<%$row.id%>&flxid=<%$GET.id%>&table=<%$GET.table%>','Variable Editor')"><i class="far fa-edit"></i></button>                     
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
        </table>
         <%$subbtn%>
        </form>
<%else%>
    <div class="alert alert-info">Keine Data-Set Felder angelegt</div>
<%/if%>  