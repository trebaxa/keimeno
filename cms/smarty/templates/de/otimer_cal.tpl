<% if ($jav_prog_select_allowed=="") %>
 <div class="alert alert-warning">Für diesen Tag wurden noch keine Mitarbeiter festlegt. Eine Reservierung ist zur Zeit  nicht möglich.</div>
<% /if %>

<% if ($OTDATE_OBJ.DAY.day_closed==1) %>
 <div class="alert alert-warning">Wir sind ausgebucht! Eine Reservierung ist zur Zeit nicht möglich.</div>
<% /if %>

<% if ($OTDATE_OBJ.DAY.day_closed==0 && $jav_prog_select_allowed!="") %>
<div id="ottable" class="row">
  <div class="col-md-12">
    <table class="table table-hover table-striped">
    <% foreach from=$OTDATE_OBJ.hours_overview item=hour name=mt %>
    <tr>
    <td class="col-md-1"><span style="font-size:14pt"><%$hour%></span><sup style="font-size:80%">00</sup></td>
    <td class="col-md-11">
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
        <div>
        frei <% if ($otimer.seldatetime.timeint>=$OTDATE_OBJ.DAY.today.timeint) %> - <a class="btn btn-default btn-sm" href="<%$PATH_CMS%>index.php?page=<%$otimer.page%>&aktion=addnew&seldate=<%$otimer.seldate%>&hour=<%$hour%>">
        Jetzt reservieren</a><%/if%>
        </div>
       <%else%> 
       <% if ($otdate.span_type!='OVER') %>             
                <div class="trenner"><img style="float:left;vertical-align:middle;" src="<%$PATH_CMS%>includes/modules/otimer/css/opt_arrow_left.png" ></div>
       <%/if%>          
        <div class="otdateinfo">                
          <% if ($otdate.span_type!='OVER') %>           
                <span style="font-size:11pt"><%$otdate.timefrom.time.H%></span><sup style="font-size:80%"><%$otdate.timefrom.time.i%></sup>
                -<span style="font-size:11pt"><%$otdate.timeto.time.H%></span><sup style="font-size:80%"><%$otdate.timeto.time.i%></sup>            
                <%/if%> 
            </div>
            
            <% if ($otdate.overhead_min==0 || $otdate.lastblock==TRUE) %>
            <div class="trenner_rechts"><img src="<%$PATH_CMS%>includes/modules/otimer/css/opt_arrow_right.png" ></div>
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
</div>
<% include file="otimer_workingtime.tpl" %>
<%/if%>