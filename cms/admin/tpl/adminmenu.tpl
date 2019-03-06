<li class="dropdown hidden-sm hidden-xs"><a class="dropdown-toggle"  data-close-others="false" data-toggle="dropdown" href="#">Apps</a>
 <ul class="dropdown-menu appmenu">
    <li><%include file="appmenu.tpl"%></li>
 </ul>
</li>

<% if (is_array($RESOURCE.table) && count($RESOURCE.table)>0) %>
    <li class="dropdown hidden-sm hidden-xs"><a class="dropdown-toggle"  data-close-others="false" data-toggle="dropdown" href="#">Inhalt</a>
     <ul class="dropdown-menu appmenu">        
        <li>
            <div class="mega-menu">
                <ul>
                    <%foreach from=$RESOURCE.table item=row %>
                    <li>
                        <a title="<%$row.f_name|sthsc%>" class="ajax-link" href="run.php?epage=resource.inc&cmd=load_resource&flxid=<%$row.id%>"><%$row.f_name%>
                        <span class="description"><%$row.description|truncate:90%></span>
                        </a>
                        </li>
                    <%/foreach%>    
                </ul>
            </div>
        </li>
     </ul>
    </li>
<%/if%>

<% function name="menutree" %>    
    <%foreach from=$items item=element %>    
        <%if ($element.id|in_array:$allowed_menu_items) %>
            <li class="hidden-sm hidden-xs<%if !empty($element.children)%> dropdown<%/if%>">            
            <a class="<%if !empty($element.children)%>dropdown-toggle<%/if%><% if ($active_node.id==$element.id) %>active<%/if%>" <%if !empty($element.children)%>data-toggle="dropdown" data-delay="10" data-close-others="false"<%/if%> <%if ($element.parent>0 || (empty($element.children) && $element.parent==0)) %>href="<%$element.php%>"<%else%>href="#"<%/if%> title="<%$element.mname|hsc%>">
                <%if ($element.parent>0) %><%$element.mname%><span class="description"><%$element.description|truncate:90%></span><%else%><%$element.mname%><%/if%>
            </a> 
            <%if !empty($element.children)%>
                   <ul class="dropdown-menu"> <%call name="menutree" items=$element.children%></ul>
            <%/if%>
            </li>                     
        <%/if%>
    <%/foreach%>
<%/function%>
<% call name="menutree" items=$adminmenu %>

