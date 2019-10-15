
<% if ($paging.total_pages > 1) %>
   <nav aria-label="Page navigation">
        <ul class="pagination">
            <% if ($paging.start > 1) %>
                <li class="page-item"><a class="page-link" href="<% $paging.base_link_admin %>&start=<% $paging.startback %>">&laquo;</a></li>
            <% /if%>
          
            <% foreach from=$paging.back_pages item=link name=pp %>
                <% if ($link.index>0) %>
                    <li class="page-item"><a class="page-link" title="<%$link.index%>" href="<%$link.linkadmin%>"><%$link.index%></a></li>  
                <%/if%>
            <% /foreach %> 
            <strong><a href="#" class="page-link"><% $paging.akt_page %></a></strong>
            <% foreach from=$paging.next_pages item=link name=pp %> 
                <li class="page-item"><a class="page-link" title="<%$link.index%>" href="<%$link.linkadmin%>"><%$link.index%></a></li>      
            <% /foreach %> 
          
            <% if ($paging.start + $gbl_config.pro_max_paging.tpl < $paging.count_total && count($paging.back_pages)>1) %> 
                <li class="page-item"><a class="page-link" href="<% $paging.base_link_admin %>&start=<% $paging.newstart %>">&raquo;</a></li>
          <% /if %>
          
        </ul>
  </nav>
<% /if %>