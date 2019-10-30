<%include file="cb.panel.header.tpl" title="Bild zuschneiden"%>
    <div id="jcrob">
    
         <% foreach from=$FLEXTEMP.flextpl.datasetvarsdb item=row %>
       <%*$row|echoarr*%>  
            <%assign var="column" value=$row.v_col%>
        <% if ($row.v_type=='img' && $row.id==$GET.aid) %>
            
           
        <form method="POST" action="<%$PHPSELF%>" class="jsonform">
            <input type="hidden" value="" id="jcrobx" name="FORM[x]">
            <input type="hidden" value="" id="jcroby" name="FORM[y]">
            <input type="hidden" value="" id="jcrobx2" name="FORM[x2]">
            <input type="hidden" value="" id="jcroby2" name="FORM[y2]">
            <input type="hidden" value="" id="jcrobw" name="FORM[w]">
            <input type="hidden" value="" id="jcrobh" name="FORM[h]">
            <input type="hidden" name="cmd" value="dataset_jcropsave">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="rowid" value="<%$GET.rowid%>">
            <input type="hidden" name="content_matrix_id" value="<%$GET.content_matrix_id%>">
            <input type="hidden" name="flxid" value="<%$GET.flxid%>">
            <input type="hidden" name="column" value="<%$column%>">
            <input type="hidden" name="table" value="<%$GET.table%>">
            <input type="hidden" name="langid" value="<%$GET.langid%>">
            <input type="hidden" name="id" value="<%$row.id%>">
            
            <div class="btn-group">
                <a class="btn btn-secondary ajax-link" data-target="js-after-plugin-editor"  href="<%$eurl%>cmd=show_dataset_jcrop&rowid=<%$GET.rowid%>&column=<%$column%>&content_matrix_id=<%$GET.content_matrix_id%>&flxid=<%$GET.flxid%>&gid=<%$GET.gid%>&aid=<%$GET.aid%>"><i class="fas fa-sync"></i> reload</a>
                <button type="button" onclick="show_flxtpldataset_edit()" class="btn btn-secondary">zurück</button>
                <input type="submit" class="btn btn-primary" value="Ausschnitt neu speichern" >
            
            <div class="form-group">
                <label class="sr-only">Ration</label>
                <select class="form-control" id="js-ratio"></select>
            </div>
            </div>
        </form>        
        
                <img id="jcrobtarget" src="../file_data/flextemp/images/<%$FLEXTEMP.seldataset.row.$column|hsc%>?a=<%$randid%>" />
            <%/if%>
        <%/foreach%>
    </div>
<%include file="cb.panel.footer.tpl"%>                     

<script>

function show_flxtpldataset_edit() {
    remove_jcrop();
    simple_load('js-after-plugin-editor' ,'<%$eurl%>&rowid=<%$GET.rowid%>&cmd=show_edit_dataset&content_matrix_id=<%$GET.content_matrix_id%>&flxid=<%$GET.flxid%>&gid=<%$GET.gid%>');
    
}


$(document).ready(function (){
    scrollToAnchor('js-after-plugin-editor');           
});

</script>
<%include file="cb.jcrop.tpl" img="../file_data/flextemp/images/<%$FLEXTEMP.seldataset.row.$column|hsc%>?a=<%$randid%>"%>