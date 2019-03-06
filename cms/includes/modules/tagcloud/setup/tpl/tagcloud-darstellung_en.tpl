<% if (count($tagcloud.words)>0 && $gbl_config.tag_enabled==1) %>
<div>
<h3>Popular Tags</h3>
<% foreach from=$tagcloud.words item=tagword %>
<a style="background-color: transparent;font-size:<%$tagword.fontsize%>px;color: rgb(<%$tagword.fontcolor%>, <%$tagword.fontcolor%>, <%$tagword.fontcolor%>)" href="<%$tagword.link%>" title="<%$tagword.word%>"><% $tagword.word %></a>
<%/foreach%>
</div>
<%/if%>