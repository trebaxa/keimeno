<ul>
<% if ($PERM.core_acc_content_manager==true) %>
              <li><a data-cont="websitetree" class="js-sb-box-click" href="#" onclick="load_website_tree();"><i class="fas fa-home"></i> Homepage <span class="fa fa-chevron-down"></span></a>
                   <!--<ul class="nav child_menu knone">
                        <li id="websitetree"></li>
                   </ul>-->
                   <div id="websitetree" class="js-cont"></div>
              </li>
              <% if ($PERM.core_acc_orgatab==true) %>
                <li><a data-cont="js-orga-tree" class="js-sb-box-click" href="javascript:void(0);" id="js-open-orga-tree" ><i class="fa fa-sitemap"></i> Orga <span class="fa fa-chevron-down"></span></a>
                    <div id="js-orga-tree" class="js-cont">
                        <%include file="website.orga.tpl"%>
                    </div>
                </li>
              <%/if%>

        <script>
            function load_website_tree() {
                simple_load('websitetree','run.php?epage=websitemanager.inc&urlcmd=<%$cmd%>&cmd=load_website_tree<% if ($epage=="websitemanager.inc") %>&id=<%$GET.id%><%/if%>');
            }
            load_website_tree();
            <% if ($PERM.core_acc_orgatab==true) %>
                load_orga_tree();
            <%/if%>
        </script>
<%/if%>

<% if ($PERM.core_acc_system==true) %>
    <li>
        <a data-cont="js-settings-tree" class="js-sb-box-click" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Settings <span class="fa fa-chevron-down"></span></a>
        <div id="js-settings-tree" class="js-cont">
            <ul id="tree3">
                <% function name="systemtree" %>
                <%foreach from=$items item=element%>
                    <li>
                    <%if !empty($element.children)%>
                        <a href="javascript:;"><i class="fa <%$element.icon%>"></i> <%$element.mname%></a>
                    <%else%>
                        <a class="ajax-link <% if ($active_node.id==$element.id) %>active<%/if%>" href="<%$element.php%>" title="<%$element.mname|hsc%>"><%$element.mname%></a>
                    <%/if%>

                        <%if !empty($element.children)%>
                            <ul><%call name="systemtree" items=$element.children%></ul>
                        <%/if%>
                    </li>
                <%/foreach%>
                <%/function%><% call name="systemtree" items=$system_menu %>

            </ul>
        </div>
    </li>
    <script>
        $('#tree3').treed({openedClass:'fa fa-chevron-right', closedClass:'fa fa-chevron-down'});
    </script>
<%/if%>

    <%*APP MENU for mobil device*%>
    <li class="d-md-none d-lg-none d-xl-none"><a href="javascript:;"><i class="fa fa fa-puzzle-piece fa-lg"></i> Apps</a>
     <ul class="nav child_menu">
         <%foreach from=$app_menu item=element%>
            <%if ($element.id|in_array:$allowed_menu_items) %>
               <li>
                 <a class="ajax-link <% if ($active_node.id==$element.id) %>active<%/if%>" href="<%$element.php%>" title="<%$element.mname|hsc%>"><%$element.mname%></a>
                </li>
            <%/if%>
        <%/foreach%>
     </ul>
    </li>

    <%*Webexplorer etc*%>
    <% function name="sidemenutree" %>
    <%foreach from=$items item=element %>
        <%if ($element.id|in_array:$allowed_menu_items) %>
            <li class="d-md-none d-lg-none d-xl-none">
            <a <%if ($element.parent>0 || (empty($element.children) && $element.parent==0)) %>href="<%$element.php%>"<%else%>href="javascript:;"<%/if%> title="<%$element.mname|hsc%>">
                <%if ($element.parent==0) %><i class="fa fa <%$element.icon%> fa-lg"></i> <%/if%><%$element.mname%>
            </a>
            <%if !empty($element.children)%>
                   <ul class="nav child_menu"> <%call name="sidemenutree" items=$element.children%></ul>
            <%/if%>
            </li>
            <%/if%>
        <%/foreach%>
    <%/function%>
    <% call name="sidemenutree" items=$adminmenu %>


</ul>
