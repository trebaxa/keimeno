
     
     <form method="post"  <% if ($GET.id>0) %>class="jsonform"<%/if%> action="<%$PHPSELF%>" enctype="multipart/form-data">
     <% if ($GET.id>0) %>
        <input type="hidden" name="id" value="<%$GET.id%>">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="aktion" value="a_save">
     <%else%>
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="add_letter">        
     <%/if%> 


<%include file="cb.panel.header.tpl" title="Einstellungen"%>	
            <div class="form-group"> 
                <label>Format</label>
                <input type="radio" <% if ($NEWSLETTER.newsedit.e_html==1)%>checked<%/if%> name="FORM[e_html]" value="1">HTML
                <input type="radio" <% if ($NEWSLETTER.newsedit.e_html!=1)%>checked<%/if%> name=FORM[e_html] value="0">TEXT
            </div>  
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{LBLA_DISABLELINK_SENTENCE}:</label>
                    <input type="text" class="form-control" value="<% $NEWSLETTER.newsedit.e_unsubscribe%>" name="FORM[e_unsubscribe]">
                </div>
                 <div class="form-group col-md-6">
                    <label>{LBLA_SALUTATION_MALE}:</label>
                    <input type="text" class="form-control" value="<% $NEWSLETTER.newsedit.e_anrede_m%>" name="FORM[e_anrede_m]">
                </div>    
             </div>   
          <div class="row">
             <div class="form-group col-md-6">
                <label>{LBLA_SALUTATION_FEMALE}:</label>
                <input type="text" class="form-control" value="<% $NEWSLETTER.newsedit.e_anrede_w%>" name="FORM[e_anrede_w]">
            </div>     

            <div class="form-group col-md-6">
            <label for="datei-news">Attachments {LBLA_ADD}:</label>
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
                    <input id="datei-news" class="xform-control autosubmit" type="file" onchange="this.previousElementSibling.value = this.value" value="" name="attfile"></input>
                    <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>                
                </div>                    
            </div>
          </div>  
           
           <div id="js-newsfiles">             
             <%include file="newsletter.files.tpl"%>
           </div>	
            
            
            
	<%$subbtn%>
<%include file="cb.panel.footer.tpl"%>	
<script>
    function reloadfiles() {
        simple_load('js-newsfiles','<%$eurl%>cmd=reloadfiles&id=<%$GET.id%>');
    }
</script>

<%include file="cb.panel.header.tpl" title="Inhalt"%>
    <div class="form-group">
        <label>{LBLA_SUBJECT}</label>
        <input type="text"  class="form-control" name="FORM[e_subject]" value="<% $NEWSLETTER.newsedit.e_subject|sthsc%>">
    </div>
    <div class="form-group">    
        <label>{LBLA_CONTENT}:</label>
            <% if ($NEWSLETTER.newsedit.e_html==1)%>
                <div class="alert alert-warning">{LBL_CORREKTROOT}</div>
                <%$NEWSLETTER.htmleditor%>
            <%else%>
                <textarea class="se-html" rows="30" name="FORM[e_content]"><% $NEWSLETTER.newsedit.e_content|hsc%></textarea> 
            <%/if%>
    </div>			
    <%$subbtn%>
  <%include file="cb.panel.footer.tpl"%> 
 
</form>

<div class="row">
    <div class="col-md-6">
 <% if ($GET.id>0) %>
	 <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" class="jsonform">
    <div class="form-group"> 
        <label>Newsletter testen</label>
	    <input type="text" class="form-control" name="testemail" value="<%$FM_EMAIL%>">
     </div>   
	 <input type="hidden" name="epage" value="<%$epage%>">
     <input type="hidden" name="cmd" value="send_test_email">
	 <input type="hidden" name="id" value="<%$GET.id%>">
	 <input type="submit" class="btn btn-primary" value="{BTN_SENDTEST}"></form>
    <%/if%>
    </div>
    <div class="col-md-6">
        <label>{LBLA_LEGEND}</label><br>
        <%include file="newsletter.legend.tpl"%>
   </div> 
       </fieldset>  
</div>                                                    