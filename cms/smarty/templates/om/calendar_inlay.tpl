<div id="caldiv">
<table  width="100%"><tr>
<% assign var=line_break value=1 %>
<% foreach from=$calinlay item=monthtab name=calinlayloop %>
 <% if ($monthtab.month>=$cal_inlay.cal_month && $monthtab.month<$cal_inlay.cal_month+$gbl_config.events_inlay_month_count ) %> 
  <td style="vertical-align: top;">
  <div style="width:100%;text-align:center;border:0px solid red;">
  <% $monthtab.table %>
  </div></td>
  <% if $smarty.foreach.calinlayloop.iteration % $line_break == 0 %>
  </tr><tr>
  <% /if %>
 <% /if %>
<% /foreach %>
</tr> </table> 
</div>
