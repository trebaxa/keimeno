<div class="mega-menu <%$adminmenu_row_class%>">
                <div class="row">
                    <div class="col-md-<%$adminmenu_col_class%>">
                        <ul>
                <%foreach from=$app_menu item=element name=adminmenuloop%>
                 <%if ($element.id|in_array:$allowed_menu_items) %>
                <li>
                 <a class="ajax-link <% if ($active_node.id==$element.id) %>active<%/if%>" href="<%$element.php%>" title="<%$element.mname|hsc%>">
                <%if ($element.parent>0) %><%$element.mname%><span class="description"><%$element.description|truncate:90%></span><%else%><%$element.mname%><%/if%>
            </a>   
                </li>
                <%/if%>
                <% if $smarty.foreach.adminmenuloop.iteration % 8 == 0%>
                    </ul>
                   </div><!-- col -->
                   <div class="col-md-<%$adminmenu_col_class%>">
                   <ul> 
                <%/if%>   
                <%/foreach%>
            </ul>
          </div>    
     </div> 
</div>         