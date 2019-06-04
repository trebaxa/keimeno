<%include file="cb.panel.header.tpl" title="`$NEWSLETTER.group.group_name`" %>
<% if (count($NEWSLETTER.emailliste)>0) %>
    <table class="table table-striped table-hover" id="listmails-table">
        <tbody>
            <% foreach from=$NEWSLETTER.emailliste item=row %>		
             <tr>
                <td><% $row.email %></td>
                <%include file="cb.icons.tpl"%>
             </tr>
            <%/foreach%>
        </tbody>
    </table>
   
<%else%>
    <div class="alert alert-info">Keine EMails eingetragen</div>
<%/if%> 
<%include file="cb.panel.footer.tpl"%>