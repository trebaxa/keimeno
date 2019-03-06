<% if (count($tag_result) > 0 ) %>
<h1>{LBL_TAGSEARCH}</h1>
  <% foreach from=$tag_result item=tagitem %>
  <div style="width:100%;border-bottom: 1px solid;">
  <a href="<%$tagitem.link%>" title="<%$tagitem.title%>"><%$tagitem.title%></a><br>
  <span class="small"><%$tagitem.shortdescription%>
  <% if ($tagitem.shortdescription!="") %>...<a href="<%$tagitem.link%>" title="<%$tagitem.title%>">{LBL_READMORE}</a><%/if%>
  </span>
  </div>
  <%/foreach%>
<%/if%>