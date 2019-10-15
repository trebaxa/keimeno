<ul id="js_menu">
<% if ($PERM.core_acc_content_manager==true) %>
              <li><a data-cont="websitetree" class="" href="#" onclick="load_pages(0);"><i class="fas fa-home"></i> Homepage</a>
                   <div id="websitetree" ></div>
              </li>
              <% if ($PERM.core_acc_orgatab==true) %>
                <li>
                    <a data-cont="js-orga-tree" class="" href="javascript:void(0);" id="js-open-orga-tree" >
                    <i class="fa fa-sitemap"></i> Organisation</a>
                    <div id="js-orga-tree" >
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
        <a data-cont="js-settings-tree" class="" href="javascript:;" class="menu-toggle"><i class="fas fa-cog"></i> Settings</a>
            <div id="js-settings-tree" >
            <ul class="sub-menu">
                <% function name="systemtree" %>
                <%foreach from=$items item=element%>
                    <li>
                        <%if !empty($element.children)%>
                            <ul class="sub-sub-menu"><%call name="systemtree" items=$element.children%></ul>
                            <div class="sub-sub-link">
                                <a href="javascript:void(0)" class="menu-toggle"><i class="fa <%$element.icon%>"></i>&nbsp; <%$element.mname%></a>
                                <a href="javascript:void(0)" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
                            </div>
                        <%else%>
                            <a class="ajax-link <% if ($active_node.id==$element.id) %>active<%/if%>" href="<%$element.php%>" title="<%$element.mname|hsc%>"><%$element.mname%></a>    
                        <%/if%>
                    </li>
                <%/foreach%>
                <%/function%><% call name="systemtree" items=$system_menu %>

            </ul>
        </div>       
    </li>
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


<script>
  const mainMenuToggle = $("#js_menu > li > a");
  const subMenu = $('.sub-menu');
  const subMenuToggle = $('.sub-menu > li > a.menu-toggle');
  const subsubMenu = $('.sub-sub-menu');

  mainMenuToggle.on("click", function(e){
    //mainMenuToggle.not(this).find("ul").slideUp();
    $('#js_menu').find('.sub-menu').hide();
    mainMenuToggle.parent("li").removeClass("active");
    $(this).parent("li").addClass("active");
    $(this).parent().find('.sub-menu:first').slideToggle();
  });
</script>