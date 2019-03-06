
<legend>Struktur Absatz</legend>
      <div class="form-group">
        <label>Struktur (Anzahl Spalten):</label>
    <select class="form-control colchange" name="PLUGFORM[column_count]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.column_count==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
    </div>
   <div class="form-group">
        <label>Container</label>
    <select class="form-control" name="PLUGFORM[container]">
            <option <% if ($WEBSITE.node.tm_plugform.container=='div') %>selected<%/if%> value="div">HTML DIV</option>
            <option <% if ($WEBSITE.node.tm_plugform.container=='aside') %>selected<%/if%> value="aside">HTML5 ASIDE</option>
            <option <% if ($WEBSITE.node.tm_plugform.container=='article') %>selected<%/if%> value="article">HTML5 ARTICLE</option>
    </select>
    </div>   
   <div class="form-group">
        <label>CSS Class</label>
    <input type="text" class="form-control" name="PLUGFORM[cssclass]" value="<%$WEBSITE.node.tm_plugform.cssclass%>">
    </div> 
    

<div id="strucboxes" class="row"></div>

<script>
function build_editors(loop) {
    if ($('.structeditor').length){
        tinyMCE.triggerSave();
    }
    $( ".structeditor" ).each(function() {
       $('#strutextsaved'+$(this).attr('rel')).val($(this).val());
        var editid = 'strutext'+$(this).attr('rel');
        tinyMCE.execCommand('mceRemoveControl',false, editid); 
    });
   
    $('.structeditor').remove();
    $('.structcont').remove();
    var width = Math.floor(1160/loop);
    if (width<379) width=379;
    var elements="";
    for (var i = 1; i <= loop; i++) {
        var tcont ="";
        if ($('#strutextsaved'+i).length){
             tcont = $('#strutextsaved'+i).val();
        }
        $('#strucboxes').append('<div class="structcont col-md-4"><textarea class="structeditor" rel="'+i+'" id="strutext'+i+'" name="PLUGFORM[text]['+i+']">'+tcont+'</textarea></div>');
        if (elements!="") elements+=',';
        elements+="strutext"+i;
    }
tinymce.init({
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | print preview fullpage | forecolor code fullscreen",
        selector: ".structeditor",
        convert_newlines_to_brs: true,
        force_br_newlines: true,
        force_p_newlines: false,
        convert_fonts_to_spans: true,
        remove_script_host: true,
        relative_urls: true,
        document_base_url: "http://<%$FM_DOMAIN%><%$PATH_CMS%>",
        width: "100%",
        image_advtab: true,        
        extended_valid_elements: "header footer article section hgroup nav figure aside date style",
        height: "600",
        plugins: ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker", "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking", "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"],
        filemanager_title: "Filemanager",
        external_filemanager_path: "/js/ResponsiveFilemanager/filemanager/",
        external_plugins: {
                "filemanager": "/js/ResponsiveFilemanager/filemanager/plugin.min.js"
        }
}); 

}

$('.colchange').change(function() {
   build_editors($(this).val());
});

$('.structeditor').change(function() {
  //  $('#strutextsaved'+$(this).attr('rel')).val($(this).val());
});

build_editors($('.colchange').val());
</script>

<div style="display:none">
    <% foreach from=$WEBSITE.node.tm_plugform.text item=txt name=strucloop%>
        <textarea class="form-control" id="strutextsaved<%$smarty.foreach.strucloop.iteration%>"><% $txt|hsc %></textarea>
    <%/foreach%>
</div>
