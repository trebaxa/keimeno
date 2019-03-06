<div class="tabs">    
<ul class="tabNavigation">  
            <li><a href="#wiziq-appoint">Termine</a></li>  
            <li><a href="#wiziq-yourtime">Ihre VerfÃ¼gbarkeit</a></li>  
            <li><a href="#wiziq-video">Video</a></li>  
        </ul> 
<div id="wiziq-video">         
<% include file="video_upload.tpl" %>
</div>

<div id="wiziq-appoint"> 

<h1>Ihre Termine (Lehrer)</h1>

<% if (count($WIZIQ.tappointments)>0 )%>
<div id="wi-class-room"></div>
<table class="tab_std" width="100%">
<tr class="trheader">
<td>Beginn</td>
<td>Ende</td>
<td>Titel</td>
<td>Dauer</td>
<td></td>
</tr>
<%foreach from=$WIZIQ.tappointments item=wb %>
<tr class="<%cycle values="row1,row2"%>">
<td><% $wb.date_formated %></td>
<td><% $wb.dateend_formated %></td>
<td><% $wb.wd_eventname %></td>
<td><% $wb.wd_duration %></td>
         <td class="tdright">

<a class="class-room-link-<%$wb.id%>" href="<%$wb.wd_PresenterUrl%>">Klassenraum betreten</a>
<script>
$(document).ready(function(){
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
</script> 

       </td>
</tr>                           
<%/foreach%>
</table>
<%else %>
 <div class="infobox">Es liegen noch keine Termine f&uuml;r Sie vor.</div>
<%/if%>
<div class="bluebox mt-top">
<h3>Kontaktaufnahme</h3>
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
        <td colspan="2">Ihre Nachricht:<br>
        <textarea rows="6" cols="60" name="FORM[nachricht]"></textarea></td>
    </tr>       
</table>
<input type="hidden" name="page" value="<%$page%>">
<input type="hidden" name="cmd" value="send_msg">
<input type="submit" class="sub_btn" value="senden">
</form>
</div>
</div>

<div id="wiziq-yourtime"> 
<h1>Ihre VerfÃ¼gbarkeit</h1>

<form action="<%$PHPSELF%>" method="POST" id="wiziq-eform">
<input type="hidden" value="<%$page%>" name="page">
<input type="hidden" value="add_timespan" name="cmd">
<table  class="tab_std">
<tr>
    <td>
    <label class="big">Datum:</label>
    <input type="hidden" id="wd_date" name="FORM[ts_date]" value="<% $smarty.now|date_format:'%d.%m.%Y' %>">
        <div id="wddatetxt" class="big"><% $smarty.now|date_format:'%d.%m.%Y' %></div>
        <div type="text" id="datepicker"></div>
     </td>
     <td valign="top" width="210">
        <label>Ich bin 60min erreichbar von:
        <input type="text" name="FORM[ts_time_from]" maxlength="5" size="5" value="<% $smarty.now|date_format:'%H' %>:00" id="ts_time_from" class="validate[required]"><br>
         Uhr an.</label><!--
        <label>bis:</label><br>
        <input type="text" name="FORM[ts_time_to]" maxlength="5" size="5" value="<% $smarty.now|date_format:'%H' %>:30" id="ts_time_to" class="validate[required]"><br>
        -->
        <input type="submit" class="sub_btn" style="float:right" value="speichern">
       
    </td>   
 </tr> 
</table>
</form>
<h3>Ihre Zeiten</h3>
<table class="tab_std" width="600">
<tr class="trheader">
<td>Am</td>
<td>von</td>
<td>bis</td>
<td>Dauer</td>
<td></td>
</tr>
<%foreach from=$WIZIQ.timespans item=tts %>
<tr class="<%cycle values="row1,row2"%>">
    <td><%$tts.date_ger %></td>
    <td><%$tts.time_from %></td>
    <td><%$tts.time_to %></td>  
    <td><%$tts.duration_hours %></td>  
    <td><a href="javascript:void(0);"><img id="del-<%$tts.id%>" class="delete" src="/images/opt_del.png" ></a></td>
</tr>
<%/foreach%>
</table>
<div id="result"></div>
</div>

</div>
 
<script type="text/javascript" charset="utf-8">
$("table td img.delete").click(function () {
    simple_load('result','<%$PHPSELF%>?page=<%$page%>&cmd=axdelete_tts&id=' + $(this).attr('id'));
    $(this).parent().parent().parent().fadeTo(400, 0, function () { 
        $(this).remove();
    });
    return false;
});



$(function () {
      var tabContainers = $('div.tabs > div');
      tabContainers.hide().filter(':first').show();
                        
         $('div.tabs ul.tabNavigation a').click(function () {
                                tabContainers.hide();
                                tabContainers.filter(this.hash).show();
                                $('div.tabs ul.tabNavigation a').removeClass('selected');
                                $(this).addClass('selected');
                                return false;
       }).filter(':first').click();
});
<% if ($GET.show_vg==1) %>
$(function () {
$('div.tabs ul.tabNavigation a').filter(function(index) {
  return index==1;
}).click();
});

<%/if%>


$(document).ready(function(){
    $("#wiziq-eform").validationEngine('attach', {promptPosition : "centerRight", scroll: false});
   });

  $(document).ready(function() {
    $("#datepicker").datepicker({
        altFormat: 'dd.mm.yy',
        dateFormat: 'dd.mm.yy',
        defaultDate: '<% $smarty.now|date_format:"%d.%m.%Y" %>',
        onSelect: function(dateText, inst) { 
            $('#wddatetxt').html(dateText);
            $('#wd_date').val(dateText);
         }
        });
  });
 
 $('#wd_date').css({backgroundColor: '#ffe', borderLeft: '5px solid #ccc', color: '#A7A6A6'});
</script>
