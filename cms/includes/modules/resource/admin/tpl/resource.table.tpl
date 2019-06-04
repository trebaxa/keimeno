<% if (count($RESOURCE.table)>0)%>
    <form method="POST" action="<%$PHPSELF%>" class="form-inline jsonform">
        <input type="hidden" name="cmd" value="save_flx_table" />
        <input type="hidden" name="epage" value="<%$epage%>" />
        
      <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>Bezeichnung</th>
                    <th>Sitemap</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$RESOURCE.table item=row %>
                <tr>
                    <td>
                    <div class="form-group">
                        <label class="sr-only">Bezeichnung</label>
                        <input class="form-control" type="text" required="" name="FORM[<%$row.id%>][f_name]" value="<%$row.f_name|sthsc%>" />
                    </div>
                    </td>
                    <td>
                        <div class="checkbox">
                            <label>
                                <input <% if ($row.f_sitemap==1) %>checked<%/if%> type="checkbox" name="FORM[<%$row.id%>][f_sitemap]" value="1" />                                
                            </label>
                        </div>
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
    <div class="alert alert-info">Noch keine Inhalte angelegt.</div>
<%/if%>        