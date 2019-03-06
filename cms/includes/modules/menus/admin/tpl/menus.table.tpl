<% if (count($MENUS.table)>0)%>
         <%include file="cb.panel.header.tpl" title="Menus"%> 
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_menu_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          
      <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>manuelle Implementierung</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>            
            <% foreach from=$MENUS.table item=row %>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.m_name|sthsc%>" name="FORM[<%$row.id%>][m_name]" /></td>
                    <td><code>{TMPL_MENU_<%$row.id%>}</code></td>
                    <td class="text-right">                     
                     <div class="btn-group">
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
            </tbody>
        </table>
        <%$subbtn%>
        </form>
         <%include file="cb.panel.footer.tpl"%>
<%else%>
    <div class="alert alert-info">Keine Menüs angelegt</div>
<%/if%>  