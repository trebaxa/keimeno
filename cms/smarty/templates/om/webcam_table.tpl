<tr class="<% $sclass %>" >
    <td><% $mdate.date %><% if ($mdate.date_to!='') %> - <% $mdate.date_to %><% /if %>
    <br><span class="small"><% $mdate.time_from %> - <% $mdate.time_to %></span></td>
    <td><img src="<%$mdate.campic.thumb%>"  alt="Webcam <%$mdate.title%>"></td>
    <td><a title="<% $mdate.title %>" href="<% $mdate.detail_link %>"><strong><% $mdate.title %></strong></a><br>
<span class="small"><% $mdate.place %></span>
    <% $mdate.introduction %></td>

<td>
  <% if ($webcamobj.c_cam_online==1) %>
  <a onClick="livestream_stdpop('<% $PATH_CMS %><% $webcamobj.campic.link %>');" href="#">
      <img alt="online" title="online" src="<% $PATH_CMS %>js/plugins/webcam/cam_online.gif" ></a>
      <%else%>
           <img alt="offline" title="offline" src="<% $PATH_CMS %>js/plugins/webcam/cam_offline.gif" >
  <%/if%>
 </td>
   <% if ($customer.PERMOD.calendar.del==TRUE || $mdate.c_kid==$customer.kid ) %>
      <td>
      <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$mdate.EID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<% if ($customer.PERMOD.calendar.del==TRUE || $mdate.c_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$mdate.EID%>&page=<%$page%>&aktion=a_delnews">
<img src="<%$PATH_CMS%>images/page_delete.png" title="{LBL_DELETE}"  alt=""></a>
<%/if%> 
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($mdate.approval==1) %>0<%else%>1<%/if%>&id=<%$mdate.EID%>&page=<%$page%>">
<img title="<% if ($mdate_obj.approval!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="<%$PATH_CMS%>images/page_<% if ($mdate.approval!=1) %>not<%/if%>visible.png"  alt="">
</a></td>
    <%/if%>
</tr>
