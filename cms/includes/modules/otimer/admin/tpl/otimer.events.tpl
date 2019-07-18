<% assign var=counter value=0 %>
<div class="btn-group form-inline">
    <a class="btn btn-secondary" href="run.php?epage=<%$epage%>&aktion=addnewdate&seldate=<%$seldate_us%>"><i class="fa fa-plus"></i> {LBL_ADD_OTDATE} <% $seldateform%> {LBL_CREATE}</a>
    <a class="btn btn-secondary" href="run.php?epage=<%$epage%>&aktion=dayoptions&seldate=<%$seldate_us%>"><i class="fa fa-clock-o"></i> Arbeitszeiten vom <% $seldateform%></a>
 
<div class="btn-group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
      {LBL_OTTHEME}
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <% $sel_box %>
    </ul>
    </div> 
<div class="btn-group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
      {LBL_YEAR} <%$OTOBJ.set_year%>
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <% section name=foo start=2008 loop=$OTOBJ.future_year step=1 %>
            <li <% if ($OTOBJ.set_year==$smarty.section.foo.index) %>class="active"<%/if%>><a href="<%$PHPSELF%>?epage=otimer.inc&seldate=<%$smarty.section.foo.index%>-<%$OTOBJ.current_month%>-<%$OTOBJ.current_day%>&set_year=<%$smarty.section.foo.index%>"><%$smarty.section.foo.index%></a></li>
        <%/section%>
    </ul>
    </div>
 
 </div>



<%include file="cb.panel.header.tpl" title="Datum Auswahl"%> 
   <div class="row"> 
        <form action="<%$PHPSELF%>" method="POST" class="col-md-3">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="setotdate">
            <div class="form-group">
                <label>Datum:</label>
                <div class="input-group">
                    <input type="date" class="form-control" value="<%$seldateform%>" name="seldateger"/>
                    <div class="input-group-btn"><%$subbtngo%></div>
                </div>    
            </div>
        </form>
        <div class="col-md-9">
            <div class="alert alert-info">
                Geben Sie hier das Datum ein, welches Sie sich anzeigen lassen möchten.
            </div>
        </div>
    </div>
<%include file="cb.panel.footer.tpl" %> 
        
<%include file="cb.panel.header.tpl" title="Termine vom <% $seldateform%>"%>
    <% if (count($mdates_day)>0) %>                
    <div class="table-responsive">
        <% include file="otimer.header.tpl" %>
        <% foreach from=$mdates_day item=mdate name=mt %>
            <% include file="otimer.row.tpl" %>
            <% assign var=counter value=$smarty.foreach.mt.iteration %>
        <% /foreach %>
     </table> 
     </div>
           
     <%else%>
     <div class="alert-info alert-info">
         Es liegen noch keine Termine f&uuml;r den <% $seldateform%> vor.
     </div>
    <%/if%>
<%include file="cb.panel.footer.tpl" %> 
 

<%include file="cb.panel.header.tpl" title="{LBL_OTOVERVIEW}"%>        
    <div id='loading' class="alert alert-info" style='display:none;position:absolute'>loading...</div>
    <div id='calendar'></div>
<%include file="cb.panel.footer.tpl" %> 


<script>
	$(document).ready(function() {	
		$('#calendar').fullCalendar({
		   header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            defaultDate: moment('<%$OTIMER.selecteddate%>',"YYYY-MM-DD"),            
            selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
				$('.formele').val('');
                $('#ev_start').val($.fullCalendar.formatDate( start,'dd.MM.yyyy' ));
                calendar.fullCalendar('unselect');                
			},
			editable: true,			
			events: "<%$eurl%>cmd=load_cal_js_events&seldateger=<%$seldateform%>",
            eventRender: function(event, element) {
               
            },
            eventAfterRender : function( event, element, view ) {
                
            },
			loading: function(bool) {
				if (bool) {$('#loading').show();}
				else {
				    $('#loading').hide();
                    } 
			}			
		});	
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })	
	});
</script> 