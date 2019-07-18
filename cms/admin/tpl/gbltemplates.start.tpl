<div class="row">
    <div class="col-md-6">
    <%include file="cb.panel.header.tpl" title="System Templates auf andere Sprachen replizieren"%>
    <p class="alert alert-info">
    Ausgew&auml;hlte Sprache auf alle anderen Sprachen &uuml;bertragen. Bestehende System Tempaltes werden 
    durch die ausgew&auml;hlte Sprache &uuml;berschrieben.
    </p>		

        <form class="jsonform" method="POST" action="<%$PHPSELF%>">
        	<input type="hidden" name="cmd" value="replicate_lang">
        	<input type="hidden" name="epage" value="<%$epage%>">
            <div class="form-group">
           <label>System Templates &uuml;berschreiben mit Template Inhalt aus Sprache:</label>
           <div class="input-group"> 
            <select class="form-control custom-select" name="langid">
            	<% foreach from=$langselect item=lang %>
                        <option value="<%$lang.id%>"><%$lang.post_lang%></option>
            	<%/foreach%>
                </select>
        	   <div class="input-group-btn"><%$btngo%></div>
            </div>
           </div> 
        </form>	
        <%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-6">
<%include file="cb.panel.header.tpl" title="{LBLA_SEARCH}"%>
        <form class="gblsearchform" method="post" action="<%$PHPSELF%>">
            <input type="hidden" name="cmd" value="search">
            <input type="hidden" name="section" value="search">
            <input type="hidden" name="epage" value="<%$epage%>">
            
            <div class="form-group">                
                    <label>{LBLA_SEARCHWORD}</label>
                    <input autocomplete="off" type="text" class="form-control" name="FORM[word]" value="<% $POST.FORM.word|hsc %>">                
            </div>

            <div class="form-feet"><%$searchbtn%></div>
        </form>
    
        <div id="gblsearchresult"></div>
        <%include file="cb.panel.footer.tpl"%>
    </div>
</div>	

<script>
    function show_search_fine(responseText, statusText, xhr, $form) {
            dc_close('csearch');
            hidePageLoadInfo();
    }
        var options = {
                    target: '#gblsearchresult',  
                    type: 'POST',
                    forceSync: true,
                    beforeSubmit: showPageLoadInfo,
                    success: show_search_fine 
            };
            $('.gblsearchform').submit(function() {
                    $(this).ajaxSubmit(options);
                    return false;
            });
</script>