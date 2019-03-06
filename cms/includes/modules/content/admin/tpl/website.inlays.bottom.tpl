<% if (count($WEBSITE.i_connections_bottom )>0) %>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Webseite</th>
                <th>Position</th>
                <th></th>
            </tr>
        </thead>
        
        <% foreach from=$WEBSITE.i_connections_bottom item=row %>
            <% include file="website.inlayrows.tpl" %>
        <%/foreach%>

    </table>
<%/if%>