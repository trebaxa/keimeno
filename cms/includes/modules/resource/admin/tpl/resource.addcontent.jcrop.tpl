<%include file="cb.panel.header.tpl" title="Bild zuschneiden"%>
    <div id="jcrob">
        <% foreach from=$RESOURCE.flextpl.flexvars item=row %>   
            <% if ($row.v_type=='img' && $row.id==$GET.rowid) %>
        
        <form method="POST" action="<%$PHPSELF%>" class="jsonform">
            <input type="hidden" value="" id="jcrobx" name="FORM[x]">
            <input type="hidden" value="" id="jcroby" name="FORM[y]">
            <input type="hidden" value="" id="jcrobx2" name="FORM[x2]">
            <input type="hidden" value="" id="jcroby2" name="FORM[y2]">
            <input type="hidden" value="" id="jcrobw" name="FORM[w]">
            <input type="hidden" value="" id="jcrobh" name="FORM[h]">
            <input type="hidden" name="cmd" value="jcropsave">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="rowid" value="<%$row.id%>">
            <input type="hidden" name="content_matrix_id" value="<%$GET.content_matrix_id%>">
            <input type="hidden" name="flxid" value="<%$GET.flxid%>">
            <input type="hidden" name="langid" value="<%$GET.langid%>">
            <input type="hidden" name="id" value="<%$row.id%>">
            
            <div class="btn-group">
                <a class="btn btn-secondary ajax-link" href="<%$eurl%>cmd=show_jcrop&content_matrix_id=<%$GET.content_matrix_id%>&rowid=<%$row.id%>&flxid=<%$GET.flxid%>&langid=<%$GET.langid%>"><i class="fas fa-sync"></i> reload</a>
                <button type="button" onclick="show_resrc_edit()" class="btn btn-secondary">zurück</button>
                <input type="submit" class="btn btn-primary" value="Ausschnitt neu speichern" >
            
            <div class="form-group">
                <label class="sr-only">Ration</label>
                <select class="form-control" id="js-ratio">
                    
                </select>
            </div>
            </div>
        </form>        
        
                <img id="jcrobtarget" src="../file_data/resource/images/<%$row.value%>?a=<%$randid%>" />
            <%/if%>
        <%/foreach%>
    </div>
<%include file="cb.panel.footer.tpl"%>
<%include file="cb.jcrop.tpl"%>                     
<script>
function show_resrc_edit() {
    remove_jcrop();
    simple_load('admincontent' ,'<%$eurl%>cmd=load_resource&flxid=<%$GET.flxid%>');
    add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$GET.flxid%>&content_matrix_id=<%$GET.content_matrix_id%>','Inhalt bearbeiten');
}


</script>