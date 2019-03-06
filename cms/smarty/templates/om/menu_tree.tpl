<div id="menu_tree">
 <ul>
<% function name="recur5701" %>
   <%foreach from=$items item=element%>
   <% if ($element.id|in_array:$exclude_cids) %>
   <li>
   <%else%>
   <li><a <%if ($active_node.id==$element.id) %>class="mt_active"<%/if%> href="<%$element.catlink%>" title="<%$element.catlabel|hsc%>"><%$element.catlabel%></a>
  <%/if%>
   <%if !empty($element.children)%>
      <ul><%call name="recur5701" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=recur5701 items=$categorytreeselected %>
</ul> 
</div>
