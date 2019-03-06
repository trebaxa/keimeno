<%*$mmenu|echoarr*%>
<section>
  <div class="container">
    <h2>Menu</h2>
    <ul class="nav nav-pills nav-stacked">
    <% function name="mmenurecur" %>
       <%foreach from=$items item=element%>
       <li class="<% if ($active_node.id==$element.mm_id) %>active<%/if%>">
         <a href="<%$element.catlink%>" title="<%$element.description|sthsc%>"><%$element.description%></a>
       <%if !empty($element.children)%>
          <ul><%call name="mmenurecur" items=$element.children%></ul>
       <%/if%>
       </li>
       <%/foreach%>
    <%/function%>
    <% call name=mmenurecur items=$mmenu %>
    </ul>
  </div>
</section>