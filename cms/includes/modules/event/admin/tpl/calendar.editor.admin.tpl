<% if ($EVENT.edit_form.id>0) %>
<h3>{LBL_EDIT} - <% $EVENT.edit_form.FORM_CON.title %></h3>
<%else%>
<h3>{LBL_ADD} - <% $EVENT.seldate %></h3>
<%/if%>
<form action="<% $PHPSELF%>" <% if ($EVENT.edit_form.id>0) %>class="jsonform"<%/if%> method="post" enctype="multipart/form-data">
	<input type="hidden" name="conid" value="<% $EVENT.edit_form.FORM_CON.id %>">
	<input type="hidden" name="id" value="<% $EVENT.edit_form.id %>">
	<% if ($EVENT.edit_form.id==0) %>
		<input type="hidden" name="FORM[ndate_to]" value="<% $EVENT.seldate %>">
	<%/if%>
	<input type="hidden" name="epage" value="<% $epage %>">
	<input type="hidden" name="FORM_CON[lang_id]" value="<% $EVENT.edit_form.uselang %>">
	<input type="hidden" name="cmd" value="save_event">
<div class="row">
    <div class="col-md-6">
	   <div class="form-group">
			<label>{LBL_LANGUAGE}:</label>
			<% $EVENT.edit_form.langselect %>
		</div>		
		<div class="form-group"><label>{LBL_CALTHEME}:</label>
			<select class="form-control" name="FORM[group_id]">
			<% foreach from=$EVENT.CALTHEMES item=cal %>
		 	<option <% if ($EVENT.edit_form.event.group_id==$cal.id) %>selected <%/if%>value="<%$cal.id%>"><%$cal.groupname%></option>
			<% /foreach %></select>
		</div>		
		<div class="form-group">
			<label>{LBL_DATE}:</label>
			<% if ($EVENT.edit_form.id>0) %>
                <input  class="form-control" value="<% $EVENT.edit_form.event.date %>" name="FORM[ndate]">
            <%else%>
                <input  class="form-control" value="<% $EVENT.seldate %>" name="FORM[ndate]">
            <%/if%>                
		</div>
		<div class="form-group">
			<label>{LBL_TIME} ({LBL_FROM}-{LBL_TO}):</label>
			<div class="row">
                <div class="col-md-6">
                    <input class="form-control" value="<% $EVENT.edit_form.event.time_from %>" name="FORM[time_from]">
                    <span class="help-block">hh:mm</span>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" value="<% $EVENT.edit_form.event.time_to %>" name="FORM[time_to]">
                    <span class="help-block">hh:mm</span>
                </div>
            </div>    
            
		</div>
		<div class="form-group">
			<label>{LBL_PLACE}:</label>
			<input  value="<% $EVENT.edit_form.event.place %>" type="text" class="form-control" name="FORM[place]">
		</div>
		<div class="checkbox">
			<label>
			<input <% if ($EVENT.edit_form.event.whole_day==1) %> checked <%/if%> type="checkbox" value="1" name="FORM[whole_day]"> {LBL_YES}
            {LBL_WHOLEDAY}</label>
		</div>
		<div class="form-group">
			<label>{LBL_TITLE}:</label>
			<input class="form-control" value="<% $EVENT.edit_form.FORM_CON.title %>" name="FORM_CON[title]">
		</div>
        	<div class="form-group">
                <label>{LBL_CONTENT}:</label>
                <% $EVENT.edit_form.fck %>
            </div> 
            <div class="form-group">
                <label>Additional Script:</label>
                <textarea class="se-html" name="FORM[c_script]"><% $EVENT.edit_form.event.c_script|hsc %></textarea>
            </div> 
        </div><!--col-->
        
        <div class="col-md-6">
		<div class="form-group">
			<label>{LBL_INTRODUCTION}:</label>
			<textarea class="form-control" rows="6" cols="60" name="FORM_CON[introduction]"><% $EVENT.edit_form.FORM_CON.introduction %></textarea>
		</div>
		<div class="form-group">
			<label>URL zum Link</label>:
			<input  value="<% $EVENT.edit_form.event.c_exturl %>" type="text" class="form-control" name="FORM[c_exturl]">
            <span class="help-block">z.B. externe URL zu einer anderen Seite</span>
		</div>
	   <div class="form-group">
			<label>Tag 1</label>:
			<input  value="<% $EVENT.edit_form.event.c_tag1|sthsc %>" type="text" class="form-control" name="FORM[c_tag1]">
            <span class="help-block">z.B. OpenAir</span>
		</div>
        <div class="form-group">
			<label>Interner Link:</label>:
			<select class="form-control" name="FORM[c_ilink]">
			<% foreach from=$EVENT.edit_form.event.internal_link_arr key=tid item=label %>
		 	        <option <% if ($EVENT.edit_form.event.c_ilink==$tid) %>selected <%/if%>value="<%$tid%>"><%$label%></option>
			<% /foreach %>
            </select>
		</div>          
		<div class="form-group">
			<label>Author: <% if ($EVENT.edit_form.event.c_author!="") %> <%$EVENT.edit_form.event.c_author%> <%else%>-<%/if%></label>
			<br><strong>Author festlegen:</strong>
			<input data-cmd="ax_ksearch" data-target="ksuche_areaot" data-addon="&doaktion=set_autor&orderby=nachname&epage=<%$epage%>&id=<%$EVENT.edit_form.id%>" type="text" class="form-control live_search" placeholder="{LBLA_CUSTOMER}" autocomplete="off">
			<br>
			<div id="ksuche_areaot" name="ksuche_areaot"></div>
		</div>		
		
        <%include file="cb.dropzone.tpl" reloadFunction="get_event_img();" title="Drag & Drop Themenbild hier"  maxFiles="1" paramName="dateiicon" cmd="dragdroplogfileimage" addon="id=`$EVENT.edit_form.id`" ident="thmeimage" acceptedFiles=".jpg,.jpeg,.png"%>
           <div id="js-event-img"> 
            <%include file="calendar.editor.img.tpl"%>
           </div>
        
        <%include file="cb.dropzone.tpl" reloadFunction="reload_event_files();" paramName="datei" cmd="dragdroplogfiledatei" addon="id=`$EVENT.edit_form.id`" ident="file" acceptedFiles=".jpg,.jpeg,.png,.pdf,.docx"%>    
       
        <div id="js-event-files">
         <%include file="calendar.editor.files.tpl"%>
        </div>
        <script>
            function reload_event_files() {
                simple_load('js-event-files', '<%$eurl%>cmd=reload_event_files&id=<%$EVENT.edit_form.id%>&a=' + Math.random(10000));
            }
            function get_event_img() {
                simple_load('js-event-img', '<%$eurl%>cmd=get_event_img&id=<%$EVENT.edit_form.id%>&a=' + Math.random(10000));                
            }
        </script>
	   
    </div>		
</div>
    <% $subbtn %>
 </form>