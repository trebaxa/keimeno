<% if ($alphagroupliste2) %>
<% assign var=line_break value="10" %>
<table class="tab_std"  width="100%">
<tr class="header"><td colspan="<% $line_break %>">Index</td></tr>
<tr>
<% foreach from=$alphagroupliste2 item=alpha name=memgbloop %>
 <td width="<% math equation="x / y" x=100 y=$line_break %>%" valign="top" align="center">
<a href="<% $alpha.link %>"><% $alpha.letter %></a></td>

<% if $smarty.foreach.memgbloop.iteration % $line_break == 0 %></tr><tr><% /if %>
<% /foreach %>
</tr>
</table>
<% /if %>