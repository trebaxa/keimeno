<div class="quick-menu">
        <div class="btn-group">
            <a class="btn btn-secondary" href="javascript:void(0);" data-toggle="modal" data-target="#addgblpage" title="{LBLA_ADD}"><i class="fa fa-plus"></i> {LBLA_ADD}</a>
        </div>
</div>

<!-- Modal ADDPAGE -->
<div class="modal fade" id="addgblpage" tabindex="-1" role="dialog" aria-labelledby="addpageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form role="form" method="post" class="jsonform" action="<%$PHPSELF%>">
      <div class="modal-header">
        <h5 class="modal-title" id="addpageLabel">{LA_NEUEINHALTSSEITEANLEG}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <input type="hidden" name="cmd" value="add_gbltpl">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="FORM[modident]" value="<%$GET.modident%>">
            <div class="form-group">
                <label for="desc">{LBLA_DESCRIPTION}:</label>
                <input autofocus="" id="desc" type="text" class="form-control" name="FORM[description]" value="<% $FORM.description|hsc %>">
            </div>
      <div class="text-right"><span class="help-block">App: <%$GET.modident%></span></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <% $subbtn %>
      </div>
       </form>
    </div>
  </div>
</div>
    

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