<% if ($aktion=="showcaldetail") %>
<html>
<body>
 <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/calendar/calendar.css);
</style>
<% include file="cal_detail.tpl" %>
</body></html>

<% elseif $aktion=='edit' %>
        <% include file="calendareditor.tpl" %>

<% elseif $aktion=="showevent" %>
  <% include file="cal_detail.tpl" %>
<% else %>

 <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/tabbedMenu/tabmenu.css);
</style>

<% if ($GET.waitapp==1) %>
 <div class="infobox">Ihr Eintrag wurde gespeichert und wartet nun auf die Genehmigung zur Veröffentlichung</div>

<%/if%>
<script  src="./filesearchhover.js"></script>
<style type="text/css">   @import url(<% $PATH_CMS %>js/images/lytebox/lytebox.css);</style>
<script src="<% $PATH_CMS %>js/images/lytebox/lytebox.js"></script>
<select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
<%section name=chk start=2008 max=$cal_year_today+1 loop=$cal_year_today+1 step=1%>
<option  <% if ($cal_year==$smarty.section.chk.index) %>selected<%/if%> value="<%$PHPSELF%>?aktion=showday&page=<%$page%>&seldate=<%$smarty.section.chk.index%>-<%$cal_month%>-<%$cal_day%>"><%$smarty.section.chk.index%></option>
<%/section%>

</select>
<% if ($themes) %>
<div class="shadetabs">
<ul>
<% foreach from=$themes item=theme name=mt %>
 <li <% $theme.class %> ><a href="<% $theme.link %>"><% $theme.theme %></a></li>

<% /foreach %>
</ul>
</div>
<% /if %>


<% include file="cal_month_boxes.tpl" %>



<% if ($customer.PERMOD.calendar.add==true) %>
<div onclick="location.href='<%$PHPSELF%>?page=<%$page%>&seldate=<%$selected_date%>&aktion=edit&id=0'" >
<% html_subbtn class="btn btn-primary" value="Hinzufügen" %>
</div><br>
<br>
<%/if%>


<% if $aktion=='showday' %>
 <h1>Tagesüberblick | <% $seldate %></h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$mdates_day item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<% include file="cal_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
 </table> 
<% if $counter==0 %>Keine Einträge<% /if %>

<% /if %>

<% if $aktion=='showmonth' %>
<h1>Monatsüberblick | <% $cal_month_str %> <% $cal_year %></h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$mdates_month item=mdate name=mt %>

<% include file="cal_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
 </table> 
<% if $counter==0 %>Keine Einträge<% /if %>
<% /if %>

<% include file="cal_sorted_view.tpl" %>

<h1>Jahresüberlick</h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$mdates item=mdate name=mt %>
<% include file="cal_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
</table> 
<% if $counter==0 %>Keine Einträge<% /if %>


<% /if %>
