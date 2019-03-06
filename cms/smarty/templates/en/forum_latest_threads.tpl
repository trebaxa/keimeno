<% if (count($forum_latest_threads)>0) %>
<h3>Aktuelle Themen Beiträge</h3>

 <table class="table table-striped table-hover">
   <thead>
     <tr>
      <th>Thema</th>             
      <th>letzter Beitrag</th>             
      <th>Forum</th>             
      <th class="text-right">Antworten</th>  
      </tr>
  </thead>
  <tbody>
     <% foreach from=$forum_latest_threads item=fthread %>         
      <tr>
        <td valign="top"><a href="<%$fthread.themelink%>"><%$fthread.t_name%></a><br>
          <span class="small">von <b><% $fthread.user.username %></b> am <%$fthread.themedatetime%></span>
        </td>             
       <td valign="top"><span class="small">
          von <b><% $fthread.user.username %></b><br>
          <% if ($fthread.thread_today==true) %>
            <span class="today">{LBL_TODAY} <%$fthread.thread_time %></span>
          <% else %>
            am <%$fthread.thread_datetime %>
          <%/if%></span>
      </td>
      <td><a href="<%$fthread.forumlink%>" title="<% $fthread.fn_name|hsc %>"><% $fthread.fn_name %></a></td>
      <td class="text-right"><a href="javascript:void(0)" title="Views: <% $fthread.t_hits %>"><% $fthread.THREADCOUNT%></a></td>
    </tr>
     <%/foreach%>
    </tbody>
 </table>
 <%/if%>