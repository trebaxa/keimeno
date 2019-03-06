<% if ($tablist ) %>
<h2><%$tabobj.plugopt.title%></h2>
<table class="tab_std" width="100%">
<% foreach from=$tablist item=tabrow name=trloop %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<% if $smarty.foreach.trloop.iteration==1 %>
<% assign var=sclass value="header" %>
<% /if %>
<tr class="<% $sclass %>">
 <% foreach from=$tabrow item=tabcol name=tdloop %>
  <td><% $tabcol %></td>
 <% /foreach %>
 </tr>
<% /foreach %>
</table>
<% /if %>