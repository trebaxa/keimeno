<h1><% $collection.col_obj.col_name %></h1>
<% foreach from=$collection.members key=gid item=groups %>
<h2><%$groups.group.groupname %></h2>
<ul>
<% foreach from=$groups.customers  item=member %>
    <li><a href="<%$member.link%>"><%$member.vorname%> <%$member.nachname%></a></li>
<% /foreach %>
</ul>
<% /foreach %>