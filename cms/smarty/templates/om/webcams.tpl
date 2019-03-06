<% if ($aktion=="showcaldetail") %>
<html>
<body>
 <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/calendar/calendar.css);
</style>
<% include file="webcam_detail.tpl" %>
</body></html>
<% /if%>

<% if $aktion=='edit' %>
<% include file="webcam_editor.tpl" %>
<%/if%>

<% if $aktion=='showevent' %>
<% include file="webcam_detail.tpl" %>
<%/if%>

<% if ($aktion=='showday' || $aktion=='') %>
 <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/calendar/calendar.css);
</style>


<% include file="webcam_calender.tpl" %>


<% if ($customer.PERMOD.webcam.add==true) %>
<div onclick="location.href='<%$PHPSELF%>?page=<%$page%>&gid=<%$newsgroup.group_id%>&aktion=edit&id=0'"><% html_subbtn class="sub_btn" value="LBL_ADD" %><div><br>
<br>
<%/if%>



<% if (count($webcam_days)>0) %>
<% if $aktion=='showday' %>
 <h1>TagesÃ¼berblick | <% $seldate %></h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$webcam_days item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<% include file="webcam_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
 </table> 

<% /if %>
<%else%>
Keine EintrÃ¤ge
<% /if %>

<h1>MonatsÃ¼berblick | <% $cal_month_str %> <% $cal_year %></h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$webcam_month item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
        <% include file="webcam_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
 </table> 
<% if (count($webcam_month)==0) %>Keine EintrÃ¤ge<% /if %>


<% if $aktion=='showyear' %>
<h1>JahresÃ¼berlick</h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$webcam_year item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<% include file="webcam_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
</table> 
<% if (count($webcam_year)==0) %>Keine EintrÃ¤ge<% /if %>
<% /if %>



<% if ($customer.PERMOD.calendar.edit==true && count($not_approved_items) > 0) %>
<br>
<h3>Nicht verÃ¶ffentlichte EintrÃ¤ge</h3>
<table class="tab_std" border="1">
    <tr class="trheader">
     <td>Datum</td>
     <td>Titel</td>
    <td>Einleitung</td>
     <td>Author</td>
    </tr> 
     <% foreach from=$not_approved_items item=event%>     
     <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
        <tr class="<% $sclass %>">
         <td valign="middle" align="left"><% $event.date %></td>
         <td valign="middle" align="left"><a href="<% $event.detail_link %>"><% $event.title %></a></td>
         <td valign="middle" align="left"><% $event.introduction %></td>
<td valign="middle" align="left"><% $event.author %></td>
         <td>

         <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$event.EID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="/images/page_white_edit.png" title="bearbeiten" ></a>
<% if ($customer.PERMOD.calendar.del==TRUE || $event.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$event.EID%>&page=<%$page%>&aktion=a_delnews">
<img src="/images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($event.approval==1) %>0<%else%>1<%/if%>&id=<%$event.EID%>&page=<%$page%>">
<img title="<% if ($news_obj.approval!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="/images/page_<% if ($event.approval!=1) %>not<%/if%>visible.png"  alt="">
</a>
</td>
          </tr>
      <% /foreach %>
         </table>
<%/if%>

<% /if %>
