<img src="<%$PATH_CMS%>file_data/tcblog/<%$selected_item.image%>" style="max-width:100%;margin-bottom:10px;" alt="<% $selected_item.title|sthsc %>">
<article class="threecol">
<h2><% $selected_item.title %></h2>

<strong>{LBL_CREATED}:</strong>
<% $selected_item.date %>

<% $selected_item.content %>
</article>
