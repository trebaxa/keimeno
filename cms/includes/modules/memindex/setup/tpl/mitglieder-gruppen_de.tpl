<% if ($membergroups) %>
<% assign var=line_break value="3" %>
<table class="tab_std" border="1" width="100%">
<tr class="header"><td colspan="<% $line_break %>">Gruppen</td></tr>
<tr>
<% foreach from=$membergroups item=memgroup name=memgbloop %>
 <td width="<% math equation="x / y" x=100 y=$line_break %>%" valign="top" align="center">
<a href="<% $memgroup.link %>"><% $memgroup.groupname %>(<% $memgroup.KCOUNT %>)</a></td>

<% if $smarty.foreach.memgbloop.iteration % $line_break == 0 %></tr><tr><% /if %>
<% /foreach %>
</tr>
</table>
<% /if %>

