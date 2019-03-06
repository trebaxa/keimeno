<h3>Forum Suchergebnis</h3>

<% if (count($FORUM.searched_themes.search_result)>0) %>
<table class="table table-hover table-striped">
    <thead>
    <tr>
        <th></hd>
        <th>Thema</th>
        <th>Antworten</th>
        <th>Aufrufe</th>
        <th>Letzter Beitrag</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <% foreach from=$FORUM.searched_themes.search_result item=ftheme %> 
    <tr>
        <td width="22">
       <% if ($ftheme.hastodaythread==true) %>
           <i class="fa fa-file text-danger"><!----></i>
         <%else%>   
            <i class="fa fa-file"><!----></i>
         <%/if%>        
        </td>
        <td><a href="<%$ftheme.themelink%>"><% $ftheme.t_name %></a></td>
        <td><%$ftheme.t_thread_count %></td>
        <td><%$ftheme.t_hits%></td>
        <td><% if ($ftheme.lastthread.id>0) %> <%$ftheme.lastthread.thread_datetime%> <%else%> 
        - <%/if%> </td><td>
<% if ($customer.kid>0 && ($customer.PERMOD.forum.del==TRUE)) %> 
    <a onClick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?fid=<%$GET.fid%>&page=<%$page%>&tid=<%$ftheme.TID%>&aktion=deltheme">
    <img src="<% $PATH_CMS %>images/opt_del.png" ></a>
<%/if%>         
</td>
    </tr>
    <%/foreach%>
    </tbody>
</table>
<%else%>
  <div class="alert alert-info">Keine passenden Themen gefunden.</div>
<%/if%>