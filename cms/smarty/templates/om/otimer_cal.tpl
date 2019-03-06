<% if ($jav_prog_select_allowed=="") %>
 <div class="infobox">FÃ¼r diesen Tag wurden noch keine Mitarbeiter festlegt. Eine Reservierung ist zur Zeit 
 nicht mÃ¶glich.</div>
<% /if %>

<% if ($OTDATE_OBJ.DAY.day_closed==1) %>
 <div class="infobox">Wir sind ausgebucht! Eine Reservierung ist zur Zeit nicht mÃ¶glich.</div>
<% /if %>

<% if ($OTDATE_OBJ.DAY.day_closed==0 && $jav_prog_select_allowed!="") %>
<div id="ottable">
<table border="1" class="tab_std" width="600">
<% foreach from=$OTDATE_OBJ.hours_overview item=hour name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<tr class="<%$sclass%>">
<td width="45" valign="top"><span style="font-size:14pt"><%$hour%></span><sup style="font-size:80%">00</sup></td>
<td width="500">
<% foreach from=$clock_table item=darr name=loopday %>
 <% if ($darr.hour==$hour) %>
 <% foreach from=$darr.dates item=otdate name=loop %>
 <% if ($otdate.span_type=='FREE') %> <% assign var=otclass value="otfree" %> <% else %>   <% assign var=otclass value="otbusy" %>    <% /if %>
 <% if ($otdate.span_type=='OVER' && $lastdate.span_type=='OVER') %> <% assign var=otclass value="otover" %>  <% /if %>
 <% if ($otdate.span_type=='FREE' && $lastdate.span_type=='OVER') %> <% assign var=otclass value="otfree" %>  <% /if %>
 <% assign var=lastdate value=$otdate %>
   <div class="<%$otclass%>" style="width:<%$otdate.width_procent%>%;">
   
   <% if ($otdate.span_type=='FREE') %>
    <div class="trenner"></div>
    <div style="margin-top:6px;height:auto;">
    frei <% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %> - <a href="<%$PATH_CMS%>index.php?page=<%$otimer.page%>&aktion=addnew&seldate=<%$otimer.seldate%>&hour=<%$hour%>">
    Jetzt reservieren</a><%/if%>
    </div>
   <%else%> 
   <% if ($otdate.span_type!='OVER') %>             
            <div class="trenner"><img style="float:left;vertical-align:middle;" src="<%$PATH_CMS%>js/plugins/otimer/opt_arrow_left.png" ></div>
   <%/if%>          
    <div class="otdateinfo">                
      <% if ($otdate.span_type!='OVER') %>           
            <span style="font-size:11pt"><%$otdate.timefrom.time.H%></span><sup style="font-size:80%"><%$otdate.timefrom.time.i%></sup>
            -<span style="font-size:11pt"><%$otdate.timeto.time.H%></span><sup style="font-size:80%"><%$otdate.timeto.time.i%></sup>            
            <%/if%> 
        </div>
        
        <% if ($otdate.overhead_min==0 || $otdate.lastblock==TRUE) %>
        <div class="trenner_rechts"><img src="<%$PATH_CMS%>js/plugins/otimer/opt_arrow_right.png" ></div>
        <%/if%>
        <% if ($otdate.span_type=='OVER') %>
         <% if ($otdate.lastblock==TRUE) %>
              <div class="otovertext">-<span style="font-size:11pt"><%$otdate.timeto.time.H%></span><sup style="font-size:80%"><%$otdate.timeto.time.i%></sup>    
              </div>          
        <%/if%>
    <%/if%>
   <%/if%>
   
   </div>
  <%/foreach%>
 <% /if%>
<%/foreach%>
</td>
</tr>
<%/foreach%>
</table>
</div>
<% include file="otimer_workingtime.tpl" %>
<%/if%>
