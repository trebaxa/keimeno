<% if ($cmd=='' || $cmd=='load_forum_js') %> 

<div class="alert alert-info text-center">
  <h4>Forum Neu-Eröffnung am 15.09.2016</h4>
</div>

<% include file="forum_latest_threads.tpl" %>
<h3>Support-Foren</h3>
<% if (count($FORUM.fgroups)>0) %>
<table class="table table-striped table-hover">
    <% foreach from=$FORUM.fgroups item=fgroup %>
    <thead>
      <tr>
          <th></th>
          <th><% $fgroup.fg_name %></th>   
          <th>Themen</th>
          <th>Beiträge</th>    
          <th>letzter Beitrag</th> 
          <% if ($FORUM.user.kid>0) %> 
          <th></th>
          <%/if%>
      </tr>
    </thead>
    <tbody>
      <% foreach from=$fgroup.foren item=forum %> 
      <tr>
          <td width="26">
           <% if ($forum.hastodaythread==true) %>
              <i class="fa fa-folder-open-o fa-2x text-danger" aria-hidden="true"><!----></i>
           <%else%>   
              <i class="fa fa-folder-open-o fa-2x" aria-hidden="true"><!----></i>
           <%/if%>
          </td>
          <td>
            <a href="<%$forum.forumlink%>"><% $forum.fn_name %></a><br>
            <span class="small"><%$forum.fn_shortdesc%></span>
          </td>        
          <td><%$forum.THEMECOUNT%></td>        
          <td><%$forum.THREADCOUNT%></td>
          <td>
            <% if ($forum.lastthread.f_time>0) %>
              <%$forum.lastthread.datetime%>
              <br><span class="small">von <b><%$forum.lastthread.user.username%></b></span>
            <%else%>
              -
            <%/if%>
          </td>
            <% if ($FORUM.user.kid>0) %> 
            <td>
                <a class="btn btn-default btn-sm" title="Neues Thema eröffnen" href="<%$PATH_CMS%>index.php?fid=<%$forum.id%>&page=<%$page%>&cmd=newtheme"><i class="fa fa-plus"><!----></i></a>    
            </td>
          <%/if%>
          
      </tr>
      <%/foreach%>
    <%/foreach%>
    </tbody>
</table>
<%else%>
  <div class="alert alert-info">Keine Gruppen gefunden.</div>
<%/if%>

<%/if%>