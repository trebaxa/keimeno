<div id="toplevel_menu">
  <% if ($cmstoplevels.count > 0) %>
  <ul>
  <% foreach from=$cmstoplevels.list item=topl%>
   <li><a <% if ($cmstoplevels.toplobj.TOPID==$topl.TOPID) %>class="selected"<%/if%> target="<%$topl.target%>" title="<%$topl.title|hsc%>" href="<%$topl.link%>"><%$topl.title%></a></li>
  <%/foreach%>
  </ul>
  <%/if%>
  </div>
