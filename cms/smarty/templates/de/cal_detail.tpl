<h1><% $selected_appointment.title %> | <% $selected_appointment.date %> </h1>

<table class="tab_cal" >
<tr>
 <td><strong>{LBL_PLACE}:</strong></td><td><% $selected_appointment.place %></td>
</tr>
<% if ($selected_appointment.whole_day==1) %>
<tr>
 <td><strong>{LBL_TIME}:</strong></td><td>ganztägig</td>
</tr>
<%else%>
<tr>
 <td><strong>{LBL_TIME}:</strong></td><td>{LBL_FROM}&nbsp;<% $selected_appointment.time_from %>&nbsp;{LBL_TO}&nbsp;<% $selected_appointment.time_to %></td>
</tr>
<%/if%>
<tr>
 <td colspan="2"><strong>{LBL_CALCONTENT}:</strong><br><% $selected_appointment.content %>
  <% if ($gbl_config.cal_gm_enable==1 && $selected_appointment.c_gm_place!="" ) %>
    <% $selected_appointment.gm %>
 <%/if%>
 </td>
</tr>
<% if (count($selected_appointment.filelist) > 0)%>
<tr>
    <td>Anh&auml;nge:</td>
    <td>    
    <table class="tab_std">
    <% foreach from=$selected_appointment.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" target="_afile" href="/<%$EVENT_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.thumbnail!="") %>
                    <img src=".<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
                    <%else%>
                    <%$afile.f_ext%>
                <%/if%>
                <td class="text-right">
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$selected_appointment.a_group_id%>&artid=<%$selected_appointment.AID%>&id=<%$afile.id%>&page=400&aktion=a_delfile">
<img src="<%$PATH_CMS%>images/page_delete.png" title="löschen"  alt="löschen"></a>                
                </td>
            </tr>
            <% /foreach %>
        </table>        
        </td>
</tr>
<% /if %>
</table>
