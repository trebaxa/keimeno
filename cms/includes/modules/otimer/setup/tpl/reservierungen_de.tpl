<style type="text/css">
   @import url(<% $PATH_CMS %>js/plugins/otimer/otimer.css);
  @import url(<% $PATH_CMS %>js/images/tabbedMenu/tabmenu.css);
@import url(<% $PATH_CMS %>js/images/calendar/calendar.css);
</style>

<div id="gblrescont">

<% if ($date_activated==true)%>
 <div class="okbox">Vielen Dank für Ihre Buchung. Diese wurde nun verbindlich eingetragen.</div>
<%/if%>

<% if ($cuinblacklist==true)%>
 <div class="infobox">Bitte wenden Sie sich direkt an uns: <% $gbl_config.adr_telefon %></div>
<%/if%>

<% if ($kregdone==1)%>
 <div class="okbox">Sie müssen diesen Termin bestätigen. Sie haben soeben eine Aktivierungsemail erhalten. Bitte rufen Sie nun Ihre Emails ab und klicken dort auf den entsprechenden Link.
Ihre Reservierung wird erst dann akzeptiert und gebucht.</div>
<br>
<a href="/index.php?page=<%$otimer.page%>">Zurück zu den Reservierungen</a>
<%else%>

<% if ($otimer.themes && $otimer.themes.count>1) %>
<div class="shadetabs">
<ul>
<% foreach from=$otimer.themes item=theme name=mt %>
 <li <% $theme.class %> ><a href="<% $theme.link %>"><% $theme.theme %></a></li>
<% /foreach %>
</ul>
</div>
<% /if %>

<% if ($otimer.aktion=='showemploytab') %>
 <% include file="otimer_employtimes.tpl" %>
<% /if %>

<% if ($otimer.aktion=='' || $otimer.aktion=='showday') %>
<h1>Reservierungen vom <%$otimer.seldatetime.date_ger%></h1>
<% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %>
<div style="text-align:center">
<a href="<%$PATH_CMS%>index.php?page=<%$otimer.page%>&aktion=addnew&seldate=<%$otimer.seldate%>">Jetzt online reservieren</a></div>
<%/if%>
 <% include file="otimer_caltable.tpl" %>
 <% include file="otimer_cal.tpl" %>
<%/if%>

<% if ($otimer.aktion=='kreg') %>
 <% include file="otimer_kreg.tpl" %>
<%/if%>

<% if ($otimer.aktion=='addnew') %>
<h1>Jetzt online reservieren! - <%$otimer.seldatetime.date_ger%></h1>
 <% include file="otimer_edit.tpl" %>
<% include file="otimer_cal.tpl" %>
<%/if%>




<%/if%>
</div>
