<% if (count($forum_latest_threads)>0 && $aktion=="") %>
<h1>Aktuelle Themen BeitrÃ¤ge</h1>
<br>
 <table class="tab_std" width="100%">
     <tr class="header">
<td>Thema</td>             
<td>letzter Beitrag</td>             
<td>Forum</td>             
<td>Replies/Views</td>  
</tr>
 <% foreach from=$forum_latest_threads item=fthread %>         
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %>
    <% else %> <% assign var=sclass value="row1" %> <% /if %>
    <tr class="<%$sclass%>">
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
<td style="text-align:right"><% $fthread.THREADCOUNT%>/<% $fthread.t_hits %></td>
</tr>

 <%/foreach%>

 </table>
 <%/if%>
