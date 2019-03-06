<style>
.otbusy,.otover {
 height:100%;
 background-color:#FFD6A0;
 border:0px #a9a9a9 solid;
 margin:0;
 vertical-align:middle;
 padding:0;
 margin-bottom:1px;
 border-bottom:1px solid #A3A3A3;
}
.otbusy,.otover {
 float:left;
 overflow:auto;
}

.otfree,.otfree_right {
 height:100%;
 background-color:#D0ED77;
 border:0px #a9a9a9 solid;
 margin:0;
 padding:0;
 text-align:center;
 vertical-align:middle;
 margin-bottom:1px;
 border-bottom:1px solid #A3A3A3;
}
.otfree {
 float:left;
}
.otfree_right {
 float:right;
}

.trenner,.trenner_rechts {
height:100%;
width:auto;
border:0px;
margin:0;
padding:0;
background-color:transparent;
//vertical-align:middle;
}

.trenner {
 float:left;
 border-left:1px dotted #000000;
  line-height:100%;
 vertical-align:bottom;
}
.trenner_rechts {
 float:right;
 border-right:1px dotted #000000; 

}
.trenner_rechts img {
 vertical-align:bottom;
 float:right;
}
.otdateinfo {
	padding:1px;
	float:left;
	width:auto;
}
.otdateinfo span,.otovertext span{
 font-weight:bold;
 font-size:11pt;
}
.otovertext{
 float:right;
 text-align:right;
 font-size:11pt;
}
#ottable {
 /*float:left;*/
}
#ottable td{
 height:60px;
 vertical-align:top;
}
</style>
<%include file="cb.panel.header.tpl" title="Übersicht"%> 
<div id="ottable">
<table  class="table table-striped table-hover">
<% foreach from=$OTDATE_OBJ.hours_overview item=hour name=mt %>

<tr>
<td width="35" valign="top"><span style="font-size:14pt"><%$hour%></span><sup style="font-size:80%">00</sup></td>
<td width="955">
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
    <div style="border:0px solid #ff0000;position:relative;top:46%;height:auto;">frei</div>
   <%else%> 
   <% if ($otdate.span_type!='OVER') %>   			
   			<div class="trenner"><img style="float:left;vertical-align:middle;" src="../includes/modules/otimer/admin/images/ot_arrow_left.png" ></div>
   <%/if%>			
    <div class="otdateinfo">     			
      <% if ($otdate.span_type!='OVER') %>           
   			<span style="font-size:11pt"><%$otdate.timefrom.time.H%></span><sup style="font-size:80%"><%$otdate.timefrom.time.i%></sup>
   			-<span style="font-size:11pt"><%$otdate.timeto.time.H%></span><sup style="font-size:80%"><%$otdate.timeto.time.i%></sup>   			
   			<br><strong><i><%$otdate.prog_title%></i></strong>
   			<br>ausgef&uuml;hrt von:<%$otdate.prog_employee%>
   			<br>Kunde:<%$otdate.nachname%>,<%$otdate.vorname%><br>
     		<%/if%>	
		</div>
		
		<% if ($otdate.overhead_min==0 || $otdate.lastblock==TRUE) %>
		<div class="trenner_rechts"><img src="../includes/modules/otimer/admin/images/ot_arrow_right.png" ></div>
		<%/if%>
		<% if ($otdate.span_type!='OVER') %>
		 <div class="pull-right">
          <div class="btn-group">
		 	<%$otdate.icon_del%>
		 	<%$otdate.icon_edit%>
		 	<%$otdate.icon_approve%>
           </div> 
		 	</div>
 		<%else%>
   		<% if ($otdate.lastblock==TRUE) %>
   		<div class="otovertext">	
   		-<span><%$otdate.timeto.time.H%></span><sup style="font-size:80%"><%$otdate.timeto.time.i%></sup>   	
   		
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
<%include file="cb.panel.footer.tpl" %> 