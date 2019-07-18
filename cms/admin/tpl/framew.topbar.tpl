   <!-- top tabs -->
    <% if (is_array($ADMIN.topmenu) && count($ADMIN.topmenu)>0) %>
     <!-- <div class="topbar-bkg clearfix"> -->
     <div class="tc-tabs-box">
        <ul id="actionmenu" class="nav" role="tablist">
            <% foreach from=$ADMIN.topmenu item=row %>
                <li <% if ($row.active==true) %>class="active"<%/if%>><a class="ajax-link" title="<%$row.label|sthsc%>" href="<%$row.link%>"><%$row.label%></a></li>
            <%/foreach%>
        </ul>
      </div>  
    <%/if%>