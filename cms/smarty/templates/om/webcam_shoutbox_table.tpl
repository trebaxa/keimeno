<table class="tab_std"  width="100%" cellpadding="3">
<% foreach from=$sb_chattable item=sb %>
<tr>
<td width="150"><% $sb.datum %> <% $sb.zeit %></td>
<td width="150"><b><%$sb.username%></b></td>
<td><font color="#E3492B"><% $sb.msg %></font></td>
<td>
<% if ($sb.uid==$customer.kid) %>
<a href="<%$PHPSELF%>?event_id=<%$SHOUT.event.EID%>&aktion=a_del&id=<%$sb.id%>"><img alt="Delete" src="<% $PATH_CMS %>js/plugins/webcam/page_delete.png" ></a>
<%/if%>
</td>
</tr>
<%/foreach%>
</table>
