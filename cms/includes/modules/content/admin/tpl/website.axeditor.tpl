<div class="js-content-editor-panel">
  
  <div id="plugintab" class="tc-tabs-box">
      <ul class="nav nav-tabs bar_tabs" role="tablist">
        <li class="active"><a href="javascript:void(0);" class="tc-link active" data-ident="#ptab1">{LA_SETTINGS}</a></li>
        <li><a href="javascript:void(0);" class="tc-link" data-ident="#ptab2" id="js-tab-link-2" style="display:none">{LA_INHALTBEARBEITEN}</a></li>
      </ul>
  </div>
  
  <div class="tabs">
  
    <div class="tabvisi content-editor" id="ptab1" style="display:block">
        <form class="jsonform form" role="form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="add_content">
        <input type="hidden" name="FORM[tm_cid]" value="<% $GET.tm_cid %>">
        <input type="hidden" name="id" value="<% $WEBSITE.node.id %>">
        <input type="hidden" name="epage" value="<% $epage %>">
        <% if ($WEBSITE.node.id==0) %>
        <input type="hidden" name="after" value="<% $GET.after %>">
        <input type="hidden" name="FORM[tm_type]" value="<% $GET.tm_type %>">
        <input type="hidden" name="FORM[tm_modident]" value="<% $GET.tm_modident %>">
        <input type="hidden" name="FORM[tm_pos]" value="<% $GET.tm_pos %>">
        <input type="hidden" name="FORM[tm_plugid]" value="<% $GET.tm_plugid %>">
        <%else%>
        <input type="hidden" name="after" value="-1">
        <input type="hidden" name="FORM[tm_plugid]" value="<% $WEBSITE.node.tm_plugid %>">
        <input type="hidden" name="FORM[tm_modident]" value="<% $WEBSITE.node.tm_modident %>">
        <%/if%>
        
        <div class="form-group">
            <label for="bs-hint">Beschreibung</label>
            <input class="form-control" placeholder="Kurze Beschreibung" type="text" id="bs-hint" value="<% $WEBSITE.node.tm_hint|sthsc %>" name="FORM[tm_hint]">
        </div>
    
        
        <% if ($WEBSITE.node.tm_type=='P' || $GET.tm_type=='P') %>    
            <% if ($WEBSITE.PLUGIN.pluginobj->tpl!="") %>
                <%include file=$WEBSITE.PLUGIN.pluginobj->tpl%>
            <%/if%>
        <%/if%>
        
        <% if ($WEBSITE.node.tm_type=='S' || $GET.tm_type=='S') %>
            <fieldset><%include file="website.addsystpl.tpl"%></fieldset>
        <%/if%>
        
        <% if ($WEBSITE.node.tm_type=='W' || $GET.tm_type=='W') %>
            <fieldset><% $WEBSITE.fck %></fieldset>
        <%/if%>
        
        <% if ($WEBSITE.node.tm_type=='C' || $GET.tm_type=='C') %>
            <textarea data-theme="<%$gbl_config.ace_theme%>" class="se-html se-html-text" rows="25" name="FORM[tm_content]"><%$WEBSITE.node.tm_content|hsc%></textarea>
        <%/if%>
        
        
        
        
        <% if ($WEBSITE.node.tm_type=='C' || $GET.tm_type=='C' ) %>
            <fieldset>
                <legend>Links</legend>
                <div id="website-jsboxen" class="row">
                    <div class="col-md-6">
                        <select class="form-control jsboxclick" size="5">
                            <% foreach from=$WEBSITE.boxes.websitelinks item=row %>
                                <option value="<%$row.lkey%>"><%$row.lvalue%></option>
                            <%/foreach%>
                        </select>
                    </div><!-- /.col-md-6 -->
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" value="" name="urlbox_text" id="urlbox_text" placeholder="Link Platzhalter">
                            <span class="input-group-btn">
                                <button class="btn btn-default clipboard-copy" type="button"><i class="fa fa-link"></i> {LA_ADD}</button>
                            </span>
                            </div><!-- /.input-group -->
                    </div><!-- /.col-md-6 -->
                </div><!-- /#website-jsboxen.row -->
            </fieldset>
        <%/if%>
        
        
        
            <div class="modal-footer">        
                <% if (($WEBSITE.node.tm_type=='P' || $GET.tm_type=='P') && $WEBSITE.PLUGIN.admin_link!="") %>
                <a href="<%$WEBSITE.PLUGIN.admin_link%>" class="btn btn-default">Zur App "<%$WEBSITE.PLUGIN.pluginobj->name%>"</a>
                <%/if%>
                <button type="button" class="btn btn-default js-axclose">Close</button>
                <input type="submit" onclick="toggle_off();" value="{LA_SAVE}" class="btn btn-primary" id="js-plugin-submit-btn">
            </div>
        
        </form>

        
    </div> <!-- tab1 -->
    
    <div class="tabvisi" id="ptab2">
        <div id="js-after-plugin-editor"></div>
    </div>
    
    
   </div> <!-- tabs -->

</div>

<script>

function content_saved_successful() {
    if ( typeof after_submit_plugin_form == 'function' ) { 
        after_submit_plugin_form();
    }
}
$(document).ready(function() {
    init_autojson_submit();
    set_script_editor();
     $('.jsboxclick').click(function() {
	    $('#urlbox_text').val($(this).val());
		return false;
	}); 
    $('.clipboard-copy').click(function(e) {
         var val = $('.se-html-text').val()+$('#urlbox_text').val();
         var editor_id = $('.se-html-text').parent().find('.script-editor:first').attr('id');
         var editor = ace.edit(editor_id);
         editor.setValue(val, -1); 
         e.preventDefault();
    }); 
       
    $('.js-axclose').click(function(event) {
            event.preventDefault();
            remove_all_tinymce();                           
            $('#modal_frame').modal('hide');     
            $(this).closest('.js-content-editor').fadeOut();
            $('.js-content-editor-panel').remove();
            simple_load('tplcontent','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_cont_table&id=<% $GET.tm_cid %>');                    
    });  
    <% if ($WEBSITE.node.id==0) %> 
        $('#js-plugin-submit-btn').trigger('click');
    <%/if%>
    
    $('#js-after-plugin-editor').bind("DOMSubtreeModified",function(){
        $('#js-tab-link-2').show();
        $('#plugintab').show();
    });
    
    if ($('.js-content-editor-panel').parent().hasClass('modal-body') && $('#js-tab-link-2').is(":visible")==false) {
        $('#plugintab').hide();
    }

});    
</script>