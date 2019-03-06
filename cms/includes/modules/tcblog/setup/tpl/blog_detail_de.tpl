<img src="<%$selected_item.thumb%>" style="max-width:100%;margin-bottom:10px;" alt="<% $selected_item.title|sthsc %>">
<article class="threecol">
<h2><% $selected_item.title %></h2>

<strong>{LBL_CREATED}:</strong>
<% $selected_item.date %>

<% $selected_item.content %>

<% if (count($selected_item.fotos)>0) %>
<div class="blog-row">
<h3>Bilder</h3>
<% foreach from=$selected_item.fotos item=row %>
    <img src="<%$PATH_CMS%>file_data/tcblog/fotos/<%$row.foto%>" class="blog-thumb">
<%/foreach%>
</div>
<%/if%>

<% if ($selected_item.b_ytid!="") %>
<iframe width="100%" height="315" src="//www.youtube.com/embed/<%$selected_item.b_ytid%>" frameborder="0" allowfullscreen></iframe>
<%/if%>

</article>