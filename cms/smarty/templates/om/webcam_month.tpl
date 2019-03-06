<div id="caldiv">
<table  width="100%"><tr>
<td>
<% assign var=year value=$cal_year %>
<% assign var=prevM value=$cal_month-1 %>
<% if ($prevM<=0) %>
<% assign var=prevM value=10 %>
<% assign var=cal_year value=$cal_year-1 %>
<% /if %>
<a href="<% $PHPSELF %>?aktion=<% $aktion %>&page=<% $page %>&seldate=<% $cal_year %>-<% $prevM %>-<% $cal_day %>">
<img src="<% $PATH_CMS %>js/images/calendar/prev.gif" >
</a>
</td>
<% foreach from=$year_tabs item=monthtab name=mt %>
 <% if ($monthtab.month>=$cal_month && $monthtab.month<$cal_month+3 ) %> 
  <td style="vertical-align: top;text-align:center"> <% $monthtab.table %></td>
  <% assign var=mcounter value=$mcounter+1 %>
  <% if ($mcounter==3)  %>  
  <td>
<% assign var=year value=$cal_year %>
<% assign var=nextM value=$cal_month+1 %>
<% if ($nextM>10) %>
<% assign var=nextM value=1 %>
<% assign var=cal_year value=$cal_year+1 %>
<% /if %>
<a href="<% $PHPSELF %>?aktion=<% $aktion %>&page=<% $page %>&seldate=<% $cal_year %>-<% $nextM %>-<% $cal_day %>">
<img src="<% $PATH_CMS %>js/images/calendar/next.gif" >
</a>
<br><a href="<% $PHPSELF %>?aktion=<% $aktion %>&page=<% $page %>&seldate=<% $cal_year_today %>-<% $cal_month_today  %>-<% $cal_day_today  %>">{LBL_TODAY}</a>
</td>
  </tr><tr>  <% /if %>
  <% /if %>

  
<% /foreach %>



</tr> </table> 

</div>
