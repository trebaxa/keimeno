<% if ($cmd=='load_fbg') %>
<h1>FÃ¤cher</h1>

<% if (count($WIZIQ.fbs)>0) %>
<div id="mt-faecher">
<% foreach from=$WIZIQ.fbs item=row  %>
<div class="mt-faecher-box"><h3><%$row.fbg_name%></h3>
<ul>
<% foreach from=$row.faecher item=fach%>
<li><a href="<%$PHPSELF%>?page=<%$page%>&cmd=show_fbg&id=<%$row.id%>"><%$fach.fb_title%></a></li>
<% /foreach %>
</ul>
</div>
<% /foreach %>

<div class="tc-clear"></div>
</div>
<% /if %>
<% /if %>

<% if ($cmd=='show_fbg') %>
<h1><%$WIZIQ.FB.fb_title%></h1>
<% /if %>
