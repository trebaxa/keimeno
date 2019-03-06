<h1><%$POST.setvalue%></h1>
<div id="indexsearch">
<span class="small"><%$SE.search_count %> Ergebnisse (<%$SE.search_time%> Sekunden)</span>
<% foreach from=$SE.search_result item=item %>
<div class="searchrow">
<h1><a title="<% $item.s_title %>" href="<% $item.s_url %>"><%$item.s_title%></a></h1>
<%$item.s_short%>
<br><a title="<% $item.s_title %>" href="<% $item.s_url %>" class="url"><% $item.s_url %></a>
</div>
<%/foreach%>
</div>
