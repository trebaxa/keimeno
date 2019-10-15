<%include file="cb.panel.header.tpl" title="Globale Variablen"%>
<% if (count($GBLVARS.vars)>0) %>
    <form class="jsonform form-inline-" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="save_table"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{LBLA_DESCRIPTION}</th>
                    <th>Standard Wert</th>
                    <th>Smarty Implementierung</th>
                    <th>Typ</th>
                    <th></th>
                </tr>
            </thead> 
            <tbody>       
            <% foreach from=$GBLVARS.vars item=row %>
                <tr>
                    <td><input type="text" class="form-control" name="FORM[<% $row.var_name %>][var_desc]" value="<% $row.var_desc|sthsc %>"></td>
                    <%include file="gblvars.tablesetting.tpl"%>
                    <td><code><% $row.smarty %></code></td>
                    <td><%$row.var_type%></td>
                    <%include file="cb.icons.tpl"%>
                </tr>
            <%/foreach%>
            </tbody>
        </table>
        <%$subbtn%>
    </form>
<%else%>
    <p class="alert-alert-info">Noch keine Variablen angelegt.</p>    
<%/if%>    
<%include file="cb.panel.footer.tpl"%>