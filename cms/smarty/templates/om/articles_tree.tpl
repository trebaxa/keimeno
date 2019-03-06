<div id="art_tree">
 <ul>
<% function name="recur4101" %>
   <%foreach from=$items item=element%>
   <li><a <%if ($active_node.id==$element.id) %>class="mt_active"<%/if%> href="<%$element.catlink%>" title="<%$element.catlabel%>"><%$element.catlabel%>
   </a>
   <%if !empty($element.children)%>
      <ul><%call name="recur4101" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=recur4101 items=$articles_tree %>
</ul> 
</div>
