<% if (count($flags)>1) %>
<div class="btn-group pull-right">
<% foreach from=$flags item=language %>
  <a href="<%$language.link%>" class="btn btn-default btn-sm"><%$language.post_lang%></a>
  <%*<% if ($language.bild!="") %>
  &nbsp;<a href="<%$language.link%>" target="_self"><img title="<%$language.post_lang%>" alt="<%$language.post_lang%>" src="<%$language.icon%>" ></a>
  <%else%>
  &nbsp;<a href="<%$language.link%>" target="_self"><%$language.post_lang%></a>
  <%/if%>*%>
<%/foreach%>
</div>
<%/if%>