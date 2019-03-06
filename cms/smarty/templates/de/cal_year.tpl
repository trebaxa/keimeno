<div id="caldiv">
<table class="tab_std" style="margin: auto"><tr>
<% foreach from=$year_tabs item=monthtab name=mt %>
  <td style="vertical-align: top"> <% $monthtab.table %></td>
  <% if ($smarty.foreach.mt.iteration % 1 == 0 AND $smarty.foreach.mt.iteration < 12)  %>
  </tr><tr>
  <% /if %>
<% /foreach %>
</tr> </table> 
</div>