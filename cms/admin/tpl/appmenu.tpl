<div class="app-sub-list">
    <%foreach from=$app_menu item=element name=adminmenuloop%>
        <%if ($element.id|in_array:$allowed_menu_items) %>
            <li><a class="ajax-link <% if ($active_node.id==$element.id) %>active<%/if%>" onclick="closeMenu();" href="<%$element.php%>" title="<%$element.mname|hsc%>">
                <%if ($element.parent>0) %><strong><%$element.mname%></strong><span><%$element.description|truncate:90%></span><%else%><strong><%$element.mname%></strong><%/if%>
            </a></li>
        <%/if%>
      <% if $smarty.foreach.adminmenuloop.iteration % 5 == 0%>
        </div><div class="app-sub-list">
      <%/if%>
    <%/foreach%>
  </div>
