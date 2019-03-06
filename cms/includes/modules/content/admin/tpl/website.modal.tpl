<!-- Modal SEARCH AND REPLACE-->
<div class="modal fade" id="dfsearchre" tabindex="-1" role="dialog" aria-labelledby="dfsearchreLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form class="stdform" method="post" action="<%$PHPSELF%>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="dfsearchreLabel">{LBLA_SEARCHREPLACE}</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cmd" value="searchreplace">
            <input type="hidden" name="epage" value="<%$epage%>">
            <div class="form-group">
                <label>{LBLA_SEARCHWORD}</label>
                <textarea name="FORM[word]" class="form-control" rows="10"><% $POST.FORM.word|hsc %></textarea>
            </div>
            <div class="form-group">
                <label>{LBLA_REPLACEWORD}</label>
                <textarea name="FORM[rword]" class="form-control"  rows="10"><% $POST.FORM.rword|hsc %></textarea>
            </div>                    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$replacebtn%>
      </div>
       </form>
    </div>
  </div>
</div>

<!-- Modal SEARCH -->
<div class="modal fade" id="csearch" tabindex="-1" role="dialog" aria-labelledby="csearchLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form class="searchform" role="form" method="post" action="<%$PHPSELF%>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="csearchLabel">{LA_INHALTEDURCHSUCHEN}</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cmd" value="search">
        <input type="hidden" name="section" value="search">
        <input type="hidden" name="epage" value="<%$epage%>">
        <div class="form-group">
            <label for="">{LBLA_SEARCHWORD}</label>
            <input autocomplete="off" type="text" class="form-control" name="FORM[word]" value="<% $POST.FORM.word|hsc %>"> 
        </div><!-- /.form-group -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$searchbtn%>
      </div>
       </form>
    </div>
  </div>
</div>


<!-- Modal ADDPAGE -->
<div class="modal fade" id="addpage" tabindex="-1" role="dialog" aria-labelledby="addpageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form class="" role="form" method="post" action="<%$PHPSELF%>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="addpageLabel">{LA_NEUEINHALTSSEITEANLEG}</h4>
      </div>
      <div class="modal-body">
            <input type="hidden" name="cmd" value="add_website">
            <input type="hidden" name="epage" value="<%$epage%>">
            <div class="form-group">
                <label for="desc">{LBLA_DESCRIPTION} (Administration):</label>
                <input id="desc" type="text" class="form-control" name="FORM[description]" value="<% $FORM.description|hsc %>">
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="">{LBL_TREEPOSITION}:</label>
                <% $WEBSITE.IFORM.parent_select %>
            </div><!-- /.form-group -->          
  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <% $subbtn %>
      </div>
       </form>
    </div>
  </div>
</div>

<script>
    function show_search_fine(responseText, statusText, xhr, $form) {    
            $('#csearch').modal('hide');
            hidePageLoadInfo();
    }
    
    var searchoptions = {
                target: '#webpagemanager',  
                type: 'POST',
                forceSync: true,
                beforeSubmit: showPageLoadInfo,
                success: show_search_fine 
        };
    
        $('.searchform').submit(function() {
                $(this).ajaxSubmit(searchoptions);
                return false;
        });
    

</script>