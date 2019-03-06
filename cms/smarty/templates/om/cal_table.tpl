<tr class="<% $sclass %>" >
    <% if ($mdate.icon!="") %>
    <td><img src="<%$mdate.icon%>" ></td>
    <%else%>
        <td></td>
    <%/if%>
    <td width="100"><% $mdate.date %>
    <br><span class="small"><% $mdate.time_from %> - <% $mdate.time_to %></span>
    </td>
    <td><a title="<% $mdate.title %>" href="<% $mdate.detail_link %>"><strong><% $mdate.title %></strong></a><br>
    <span class="small"><% $mdate.introduction|truncate:50 %></span>
    </td>
    <td><% $mdate.place %></td>

   <% if ($customer.PERMOD.calendar.del==TRUE || $mdate.c_kid==$customer.kid ) %>
      <td width="100">
      <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$mdate.DID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<% if ($customer.PERMOD.calendar.del==TRUE || $mdate.c_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$mdate.DID%>&page=<%$page%>&aktion=a_delnews">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($mdate.approval==1) %>0<%else%>1<%/if%>&id=<%$mdate.DID%>&page=<%$page%>">
<img title="<% if ($mdate_obj.approval!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="<%$PATH_CMS%>images/page_<% if ($mdate.approval!=1) %>not<%/if%>visible.png"  alt="">
</a></td>
    <%/if%>
</tr>
