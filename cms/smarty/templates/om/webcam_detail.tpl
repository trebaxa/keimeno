<h1><% $webcamobj.title %> | <% $webcamobj.date %> </h1>

<table  width="100%">
<tr><td>
<table class="tab_cal" >
<tr>
 <td><strong>Veranstaltungsort:</strong></td><td><% $webcamobj.place %></td>
</tr>
<tr>
 <td><strong>Uhrzeit:</strong></td><td>Von&nbsp;<% $webcamobj.time_from %>&nbsp;bis&nbsp;<% $webcamobj.time_to %>
 <br><span class="small">Dauer: <% $webcamobj.duration_hours %> Stunden</span> </td>
</tr>
<tr>
 <td><strong>CamPilot:</strong></td><td><% $webcamobj.c_author %></td>
</tr>
<tr>
 <td><strong>Online:</strong></td><td>
  <% if ($webcamobj.c_cam_online==1) %>
  <a onClick="livestream_stdpop('<% $PATH_CMS %><% $webcamobj.campic.link %>');" href="#">
      <img alt="online" title="online" src="<% $PATH_CMS %>js/plugins/webcam/cam_online.gif" ></a>
      <%else%>
           <img alt="offline" title="offline" src="<% $PATH_CMS %>js/plugins/webcam/cam_offline.gif" >
  <%/if%>
 </td>
</tr>
</table>
</td><td valign="top">
 <% if ($webcamobj.c_cam_online==1) %>
<a onClick="livestream_stdpop('<% $PATH_CMS %><% $webcamobj.campic.link %>');" href="#">
<%/if%>
<img src="<% $webcamobj.campic.thumb %>" >
 <% if ($webcamobj.c_cam_online==1) %></a><%/if%>
</td></tr></table>

<% $webcamobj.content %>
  <% if ($gbl_config.webcam_gm_enable==1 && $webcamobj.c_gm_place!="" ) %>
    <% $webcamobj.gm %>
 <%/if%>
 
<% assign var=wucount value=count($webcamobj.watch_list) %>
<% if (count($webcamobj.watch_list)>0) %>
<% foreach from=$webcamobj.watch_list item=wu name=webcamloop %>
 <%$wu.uname%>,
 <% if $smarty.foreach.webcamloop.index < $wucount %>, <% /if %>
<%/foreach%>
<%/if%>
