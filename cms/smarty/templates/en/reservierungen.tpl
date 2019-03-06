<style type="text/css">
    @import url(<% $PATH_CMS %>includes/modules/otimer/css/otimer.css);
    @import url(<% $PATH_CMS %>includes/modules/otimer/css/tabmenu.css);
    @import url(<% $PATH_CMS %>includes/modules/otimer/css/calendar.css);
</style>

<div id="gblrescont">

<% if ($date_activated==true)%>
 <div class="alert alert-success">Vielen Dank für Ihre Buchung. Diese wurde nun verbindlich eingetragen.</div>
<%/if%>

<% if ($cuinblacklist==true)%>
 <div class="alert alert-info">Bitte wenden Sie sich direkt an uns: <% $gbl_config.adr_telefon %></div>
<%/if%>

<% if ($kregdone==1)%>
  <div class="alert alert-success">Sie müssen diesen Termin bestätigen. Sie haben soeben eine Aktivierungsemail erhalten. Bitte rufen Sie nun Ihre Emails ab und klicken dort auf den entsprechenden Link.
  Ihre Reservierung wird erst dann akzeptiert und gebucht.</div>
  <div class="row">
    <div class="col-md-12 text-center"><a class="btn btn-default" href="/index.php?page=<%$otimer.page%>">Zurück zu den Reservierungen</a></div>
  </div>
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
    <h2>Reservierungen vom <%$otimer.seldatetime.date_ger%></h2>
    <% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %>
      <div class="row">
        <div class="col-md-12 text-center">
          <a class="btn btn-primary" href="<%$PATH_CMS%>index.php?page=<%$otimer.page%>&aktion=addnew&seldate=<%$otimer.seldate%>"><i class="fa fa-clock-o"></i> Jetzt online reservieren</a>
        </div>
      </div>  
    <%/if%>
    <% include file="otimer_caltable.tpl" %>
    <% include file="otimer_cal.tpl" %>
  <%/if%>

  <% if ($otimer.aktion=='kreg') %>
   <% include file="otimer_kreg.tpl" %>
  <%/if%>

  <% if ($otimer.aktion=='addnew') %>
    <h2>Jetzt online reservieren! - <%$otimer.seldatetime.date_ger%></h2>
    <% include file="otimer_edit.tpl" %>
    <% include file="otimer_cal.tpl" %>
  <%/if%>

<%/if%>
</div>