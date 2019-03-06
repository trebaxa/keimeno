<div class="page-header"><h1>FAQ</h1></div>

<div class="btn-group"><a class="btn btn-default" href="javascript:void(0)" data-toggle="modal" data-target="#addgroup">Gruppe anlegen</a></div>


<!-- Modal -->
<div class="modal fade" id="addgroup" tabindex="-1" role="dialog" aria-labelledby="addgroupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
<form action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="add_group">    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="addgroupLabel">Gruppe hinzufügen</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Gruppenname</label>
            <input type="text" class="form-control" name="FORM[g_name]" value="">
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$addbtn%>
      </div>
      </form>
    </div>
  </div>
</div>


<% if (count($FAQ.groups)>0) %>
    <form action="<%$PHPSELF%>" method="POST" class="stdform form-inline">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="save_groups">
        <table class="table table-striped table-hover" id="faq-table">
                <thead>
                    <tr>
                        <th>Gruppe</th>
                        <th></th>
                    </tr>
                </thead>
            <% foreach from=$FAQ.groups item=row %>
                <tr>
                    <td><input type="text" class="form-control" name="FORM[<%$row.id%>][g_name]" value="<%$row.g_name%>"></td>
                    <td><a class="btn btn-default" href="javascript:void(0);" onclick="simple_load('faqitems','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_items&gid=<%$row.id%>');$('.faq_gid').val('<%$row.id%>')"><span class="glyphicon glyphicon-eye-open"><!----></span></a>
                     <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></td>
                </tr>
            <%/foreach%>
        </table>
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="faq-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%>   

<%$subbtn%>
</form>
<div id="faqitems"></div>
<%/if%>

<!-- Modal -->
<div class="modal fade" id="additem" tabindex="-1" role="dialog" aria-labelledby="additemLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
     <form action="<%$PHPSELF%>" method="POST" class="jsonform" id="faqform">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="additemLabel">Beitrag</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="save_item">
        <input type="hidden" name="id" class="id" value="">
        <input type="hidden" name="FORM[faq_gid]" class="faq_gid" value="<%$GET.gid%>">
        <label>Frage:</label>
        <input class="faq_question form-control" type="text" placeholder="Frage" name="FORM[faq_question]">
        <label>Anwort 1:</label>
        <textarea class="faq_answer tiny form-control" style="width:100%;height:100px" placeholder="Ihre Antwort 1 zur Frage" name="FORM[faq_answer]"></textarea>
        <label>Anwort 2:</label>
        <textarea class="faq_answer_2 tiny form-control" style="width:100%;height:100px" placeholder="Ihre Antwort 2 zur Frage" name="FORM[faq_answer_2]"></textarea>
        <label>Anwort 3:</label>
        <textarea class="faq_answer_3 tiny form-control" style="width:100%;height:100px" placeholder="Ihre Antwort 3 zur Frage" name="FORM[faq_answer_3]"></textarea>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
function init_faqs_tiny() { 
tinymce.init({
        menubar: false,       
        toolbar: "undo redo | bold italic | styleselect | alignleft aligncenter alignright alignjustify | bullist numlist | image link | code",    
        selector : ".tiny",
        convert_newlines_to_brs : true,
        force_br_newlines : true,
        paste_data_images: true,
        force_p_newlines : false,
        convert_fonts_to_spans : true,        
        remove_script_host : true,
        relative_urls : true,
        document_base_url : "http://<%$FM_DOMAIN%><%$PATH_CMS%>", 
        width: "100%",
        image_advtab: true,
        extended_valid_elements : "header footer article section hgroup nav figure aside date style",      
        height: "100",
        plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
        ], 
        filemanager_title:"Filemanager",
        external_filemanager_path:"/js/ResponsiveFilemanager/filemanager/",
        external_plugins: { "filemanager" : "/js/ResponsiveFilemanager/filemanager/plugin.min.js"}
});
}
</script>
