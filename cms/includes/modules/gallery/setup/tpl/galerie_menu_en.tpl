<div id="menu_tree">
 <ul>
<% function name="recur99701" %>
   <%foreach from=$items item=element%>
   <li><a <%if ($active_node.id==$element.id) %>class="mt_active"<%/if%> href="<%$element.catlink%>" title="<%$element.catlabel%>"><%$element.catlabel%>
   </a>
   <%if !empty($element.children)%>
      <ul><%call name="recur99701" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=recur99701 items=$gallery_tree %>
</ul> 
</div>
