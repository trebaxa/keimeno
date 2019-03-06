<ol class="breadcrumb">
    <li><a href="/index.jade.html">Startseite</a></li>
    <% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread %>
        <% if ($bread.id!=$page) %>
            <li><a title="<%$bread.linkname|hsc%>" href="<%$bread.link%>"><%$bread.label%></a></li>
        <%else%>    
            <li class="active"><%$bread.label%></li>
        <%/if%>
    <%/foreach%>
</ol>