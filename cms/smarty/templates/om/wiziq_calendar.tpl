<h1>Meine Termine</h1>
<h2><% $WIZIQ.customer.vorname%> <% $WIZIQ.customer.nachname %></h2>
<div class="clear"></div>



<div id="bigheaderight" class="aright">
 <table border=1>
    <tr class="header">
    <td>gebucht (h)</td>
    <td>verf&uuml;gbar (h)</td>
    <td>gesamt (h)</td>
    </tr>
    <tr class="info">
    <td><% $WIZIQ.budget_status.booked_budget %></td>
    <td><% $WIZIQ.budget_status.budget_left %></td>
    <td><% $WIZIQ.budget_status.total_budget %></td>
    </tr>
 </table>
 <div class="clear"></div>

</div>
<div class="clear"></div>


<% if (count($WIZIQ.appointments)>0 )%>
<script type='text/javascript' src='js/plugins/popup/jquery.popupWindow.js'></script>
<div class="bluebox">
<div id="wi-class-room"></div>
<div id="mta-tab">
<table class="mta-tab" width="100%">
<tr class="trheader">
<td>Datum</td>
<td>Beginn</td>
<td>Ende</td>
<td>Titel</td>
<td>Dauer</td>
<td></td>
</tr>
<%foreach from=$WIZIQ.appoints item=wb %>
<tr class="<%cycle values="row1,row2"%>">
<td><% $wb.date_ger %></td>
<td><% $wb.time_start %></td>
<td><% $wb.time_end %></td>
<td><% $wb.wd_eventname %></td>
<td><% $wb.wd_duration %></td>
         <td class="tdright">
<%if ($wb.pending==true ) %>
<a class="class-room-link-<%$wb.id%>" href="<%$wb.wd_CommonAttendeeUrl%>">Klassenraum betreten</a>



<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
$(".class-room-link-<%$wb.id%>").fancybox({
           'width'             : '95%',
           'height'            : '95%',
           'overlayOpacity'    : 0.6,
           'overlayColor'      : '#000000',           
           'autoScale'         : false,
           'autoDimensions'    : false,    
           'transitionIn'      : 'none',
           'transitionOut'     : 'none',
           'type'              : 'iframe'
    });
   
});

//]]>
</script>

<%else%>
-
<%/if%>
       </td>
</tr>                           
<%/foreach%>
</table>
</div>

</div>


<%else %>
 <div class="infobox">Es liegen noch keine Termine f&uuml;r <% $WIZIQ.customer.vorname%> <% $WIZIQ.customer.nachname %> vor.</div>
<%/if%>

<div class="bluebox mt-top">
<% if ($WIZIQ.budget_status.budget_left>0) %>
<h3>Mein Wunschtermin - Kontaktaufnahme</h3>
<form action="<%$PHPSELF%>" method="POST">
<table >
    <tr>
        <td width="100">Nachname:</td>
        <td><%$customer.nachname%></td>
    </tr>
    <tr>
        <td>Vorname:</td>
        <td><%$customer.vorname%></td>
    </tr> 
    <tr>
        <td colspan="2">Deine Nachricht:<br>
        <textarea rows="6" cols="60" name="FORM[nachricht]"></textarea></td>
    </tr>       
</table>
<input type="hidden" name="page" value="<%$page%>">
<input type="hidden" name="cmd" value="send_msg">
<input type="submit" class="sub_btn" value="senden">
</form>
<%else%>
<h3>Buchen</h3>
Buche jetzt weitere Stunden. Klicke hier: <input type="button" value="buchen" onClick="location.href='<% $HTA_CMSFIXLINKS.GH_URL %>'">
<%/if%>
</div>

<div class="clear"></div>
<div class="aright info float-right">
<a target="_blank" href="http://get.adobe.com/de/flashplayer/"><img style="float:left;margin:0px 6px 30px 0;" src="/file_server/template/Flash_32.png" ></a>
Es muss der Adobe Flash Player installiert sein, um am Unterricht teilzunehmen.
Klicke einfach auf das Flash Icon.<br>

</div>
