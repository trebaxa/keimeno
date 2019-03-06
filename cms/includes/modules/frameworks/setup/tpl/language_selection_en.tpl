<% if (count($flags)>1) %>
<% foreach from=$flags item=language %>
<% if ($language.bild!="") %>
&nbsp;<a href="<%$language.link%>" target="_self"><img title="<%$language.post_lang%>" alt="<%$language.post_lang%>" src="<%$language.icon%>" ></a>
<%else%>
&nbsp;<a href="<%$language.link%>" target="_self"><%$language.post_lang%></a>
<%/if%>
<%/foreach%>
<%/if%>