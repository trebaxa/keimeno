<div class="btn-group">
    <div class="btn-group">
        <a class="btn btn-primary" href="javascript:void(0);" title="neu anlegen" onclick="pop_new_event();"><i class="fa fa-plus"></i></a>
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
          {LBL_CALTHEME}
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <% foreach from=$EVENT.CALTHEMES item=row %>
                <li <% if ($row.id==$EVENT.calgroup_id) %>class="active"<%/if%>><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&seldate=<%$row.Y%>-<%$row.m%>-<%$row.d%>&gid=<%$row.id%>"><%$row.groupname%></a></li>
            <%/foreach%>
        </ul>
        </div>

        
        <div class="btn-group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
              {LBL_YEAR} <%$EVENT.cal_year%>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <%section name=chk start=$EVENT.FIRST_DATE.year max=$EVENT.LAST_DATE.year+1 loop=$EVENT.LAST_DATE.year+1 step=1%>
                    <li <% if ($EVENT.cal_year==$smarty.section.chk.index) %>class="active"<%/if%>><a class="ajax-link" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&seldate=<%$smarty.section.chk.index%>-<%$EVENT.cal_month%>-<%$EVENT.cal_day%>"><%$smarty.section.chk.index%></a></li>
                <%/section%>
            </ul>
        </div>
    
    
</div>


<div class="row">
    <div class="col-md-6">

<div class="tc-tabs-box mt-lg" id="tplvartabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a data-ident="#tabvartpl1" class="tc-link" href="javascript:void(0);">{LBL_OVERVIEWYEAR}</a></li>
        <li><a data-ident="#tabvartpl3" class="tc-link" href="javascript:void(0);">{LBL_OVERVIEWDAY} | <% $EVENT.seldate %></a></li>
        <li><a data-ident="#tabvartpl2" class="tc-link" href="javascript:void(0);">{LBL_OVERVIEWMONTH}</a></li>        
    </ul>
</div>

<div class="tabs">
<!-- TAB1 -->
<div id="tabvartpl1" class="tabvisi" style="display:block">
<% if (count($EVENT.mdates)>0) %>
    <h3>{LBL_OVERVIEWYEAR}</h3>
    <table class="table table-striped table-hover" >
        <% foreach from=$EVENT.mdates item=mdate name=mt %>
            <% include file="cal_row.tpl" %>
        <% /foreach %>
    </table> 
<% /if %>
</div>


<!-- TAB2 -->
<div id="tabvartpl2" class="tabvisi">
<% if (count($EVENT.mdates_month)>0) %>
<h3>{LBL_OVERVIEWMONTH}</h3>
    <table class="table table-striped table-hover" >
    <% foreach from=$EVENT.mdates_month item=mdate name=mt %>
        <% include file="cal_row.tpl" %>
    <% /foreach %>
     </table> 
<% /if %>
</div>

<!-- TAB3 -->
<div id="tabvartpl3" class="tabvisi" >
<% if (count($EVENT.mdates_day)>0) %>
    <h3>{LBL_OVERVIEWDAY} | <% $EVENT.seldate %></h3>
    <table class="table table-striped table-hover" >
        <% foreach from=$EVENT.mdates_day item=mdate name=mt %>
            <% include file="cal_row.tpl" %>
        <% /foreach %>
     </table> 
<%/if%>
</div>


</div><!--tabs-->	

</div><!--col-->

    <div class="col-md-6">
        <div id='loading' class="bg-info text-info mt-lg" style='display:none;position:absolute'>loading...</div>
        <div id='calendar'></div>
    </div>
 
</div>

<!-- Modal -->
<div class="modal fade" id="addeventpop" tabindex="-1" role="dialog" aria-labelledby="addeventpopLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="POST" class="form">
        <input type="hidden" name="cmd" value="save_event">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="FORM_CON[lang_id]" value="<% $EVENT.langid %>">
        <input type="hidden" name="FORM[group_id]" value="<% $EVENT.calgroup_id %>">
      <div class="modal-header">
        <h5 class="modal-title" id="addeventpopLabel">Eintrag hinzufügen</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
           <div class="form-group"> 
            <label>Titel</label>
            <input type="text" class="form-control" required name="FORM_CON[title]" value="" id="ev_title" placeholder="Titel des Events" class="formele">
           </div>
           <div class="form-group"> 
            <label>Datum</label>
            <input type="text" required class="form-control" name="FORM[ndate]" value="" maxlength="10" id="ev_start" class="formele" placeholder="dd.mm.YYY">
            <span class="help-block">dd.mm.YYY</span>
           </div>
           <div class="form-group">
            <label>Von</label> 
            <select class="form-control custom-select" name="FORM[time_from]" id="start_hm">
            <% foreach from=$EVENT.times item=uhrzeit  %><option value="<%$uhrzeit%>"><%$uhrzeit%></option><%/foreach%></select>
           </div>
           <div class="form-group"> 
            <label>Ende</label>
            <select class="form-control custom-select" name="FORM[time_to]" id="end_hm">
            <% foreach from=$EVENT.times item=uhrzeit  %><option  value="<%$uhrzeit%>"><%$uhrzeit%></option><%/foreach%>
            </select>
           </div>
           <div class="form-group"> 
            <label>Beschreibung</label>
            <textarea class="se-html formele" id="ev_description" name="FORM_CON[content]"></textarea>
           </div>             
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>


<script>
function pop_new_event() {
    			$('.formele').val('');
                $('#ev_start').val('<%$EVENT.seldate%>');
				$('#addeventpop').modal('show');
}
	$(document).ready(function() {	
		$('#calendar').fullCalendar({
		   header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            viewRender: function(view){
                var maxDate = new Date('<%$EVENT.cal_year%>-12-31');
                if (view.start > maxDate){
                    $('#calendar').fullCalendar('gotoDate', maxDate);
                }
                if (view.start.getMonth()==11) {
                     $("#calendar .fc-button-next").hide();
                } else {
                     $("#calendar .fc-button-next").fadeIn();
                }
            },
            selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
				$('.formele').val('');
                $('#ev_start').val($.fullCalendar.formatDate( start,'dd.MM.yyyy' ));
				$('#addeventpop').modal('show');
                calendar.fullCalendar('unselect');                
			},
			editable: true,			
			events: "<%$PHPSELF%>?epage=<%$epage%>&cmd=load_cal_js_events&gid=<%$EVENT.calgroup_id%>",
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
	});
</script> 