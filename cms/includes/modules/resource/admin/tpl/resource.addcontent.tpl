<div class="page-header"><h2><%$RESOURCE.flextpl.f_name%></h2></div>

<%*$RESOURCE.flextpl|echoarr*%>

<div class="btn-group">
    <a href="#" onclick="add_show_box_tpl('<%$eurl%>cmd=show_add_content&flxid=<%$GET.flxid%>','Inhalt hinzufügen')" class="btn btn-primary"><i class="fa fa-plus"></i> hinzufügen</a>
</div>


    
<%include file="cb.panel.header.tpl" title="`$RESOURCE.flextpl.f_name`"%>         
    <div class="table-responsive" id="js-resrc-table">   
        <%include file="resource.addcontent.table.tpl"%>       
    </div>
<%include file="cb.panel.footer.tpl"%>

<script>
$( function() {
    $( "#sortable" ).sortable({
        placeholder: "highlight",
         cursor: 'move',
        update: function(event, ui) {
          var ids = $(this).sortable('toArray').toString();
          jsonexec('<%$eurl%>cmd=sort_content_table&ids='+ids);
       }
    
    });
});

function reload_resource(id, content_matrix_id,langid) {    
    simple_load('admincontent','<%$eurl%>cmd=load_resource&flxid='+id);
    var url = '<%$eurl%>cmd=get_content_json&flxid='+id+'&content_matrix_id='+content_matrix_id+'&langid='+langid;
    $.getJSON(url , function( data ) {
     $.each( data, function( key, val ) {
        
      if (val.v_type=='img' && val.value!="") {
        $('#js-dataset-img-'+val.id).find('img:first').attr('src', '../file_data/resource/images/'+val.value+'?a='+Math.random());
        $('#js-dataset-img-'+val.id).show();
       }
      });
    });    
}   

function reload_dataset(content_matrix_id, langid, table) {   
    var url ='<%$eurl%>content_matrix_id='+content_matrix_id+'&cmd=show_add_datasets_by_lang&flxid=<%$GET.flxid%>&langid='+langid+'&table='+table;
    simple_load('js-resrc-content', url);    
}
</script>

