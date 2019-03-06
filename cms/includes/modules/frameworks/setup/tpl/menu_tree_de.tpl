<ul class="nav navbar-nav">
<% function name="recur5701" %>
   <%foreach from=$items item=element%>
   <% if ($element.id|in_array:$exclude_cids) %>
   <li>
   <%else%>
   <li class="<% if ($active_node.id==$element.id) %>active<%/if%><%if !empty($element.children)%> dropdown"<%/if%>"><a class="<%if !empty($element.children)%>dropdown-toggle<%/if%>" <%if !empty($element.children)%>data-toggle="dropdown" data-close-others="false"<%/if%> href="<%$element.catlink%>" title="<%$element.catlabel|hsc%>">
   <% if ($element.t_icon_img!="") %><img src="<%$element.t_icon_img%>" alt=""><%/if%>
   <%$element.catlabel%>
       <% if ($element.t_icon_img!="") %><i class="fa fa-angle-right fa-1x"><!----></i><%/if%>
   </a>
  <%/if%>
   <%if !empty($element.children)%>
      <ul class="dropdown-menu"><%call name="recur5701" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=recur5701 items=$categorytree %>
</ul> <!-- .dropdown-menu -->