<div class="clearer"></div>
<h2>Social News</h2>
<div class="fb-fanpage-stati">
<ul class="fb-ul">
<% foreach from=$FBWP.fanpage_status.data item=row %>               
<li class="fbstat">
    <% if ($row.picture!="")%>
        <img src="<%$row.thumb%>" <%$row.size.3%> alt="<%$row.message|sthsc|truncate:30%>">
    <%/if%>
    <%$row.message%>
    </li>
<% /foreach %>
</ul>
</div>
<div class="clearer"></div>
