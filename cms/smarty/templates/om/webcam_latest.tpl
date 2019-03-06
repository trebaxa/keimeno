<table class="tab_std" width="100%">
<% foreach from=$webcam_latest_items item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<tr class="<% $sclass %>" >
    <td><% $mdate.date %><% if ($mdate.date_to!='') %> - <% $mdate.date_to %><% /if %><br>
    <span class="small"><% $mdate.time_from %> - <% $mdate.time_to %></span>
    </td>
    <td valign="top"><a title="<% $mdate.title %>" href="<% $mdate.detail_link_rel %>"><strong><% $mdate.title %></strong></a><br>
     <span class="small"><i><% $mdate.place %></i></span><br>
    <% $mdate.introduction|truncate:100 %></td>
    <td><img src="<%$mdate.campic.thumb%>" ></td>
    <td>
  <% if ($webcamobj.c_cam_online==1) %>
  <a onClick="livestream_stdpop('<% $PATH_CMS %><% $webcamobj.campic.link %>');" href="#">
      <img alt="online" title="online" src="<% $PATH_CMS %>js/plugins/webcam/cam_online.gif" ></a>
      <%else%>
           <img alt="offline" title="offline" src="<% $PATH_CMS %>js/plugins/webcam/cam_offline.gif" >
  <%/if%>
 </td>

  
</tr>
<%/foreach%>
</table>
