<link rel="stylesheet" href="../includes/modules/docsend/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>DocSend - Dokumente versenden</h1></div>

<% if ($cmd=="") %>
<div class="row">
    <div class="col-md-4">        
        <% include file="cb.panel.header.tpl" title="Dateien hochladen"%>
        <div class="dropzonecss" id="js-docsend-dropzone" >
            Drag & Drop Dateien hier
        </div>
        <div id="dropzonefeedback"></div>
        <div id="js-ds-files"></div>
    
        <small>Maximale Datei Größe: <%$DOCSEND.upload_max_filesize%> |  Maximale Datei Post Größe: <%$DOCSEND.post_max_size%></small>
        <% include file="cb.panel.footer.tpl"%>
       
    </div>   
    <div class="col-md-8">
       
        <div id="js-ds-files-table"></div>
        
       
    </div>
  </div>

<%/if%>


<script>
function reload_docsend_files() {
  simple_load('js-ds-files-table', '<%$eurl%>cmd=reload_docsend_files');
}

function ds_check() {
  var valid = false;
  
  if (parseInt($('#js-customer-kid').val())>0 && $(".js-dscheckbox:checked").length>0) {
    valid = true;
  }  
  if (valid==true) {
    $('#js-btn-dssend').prop('disabled', false);
  }
}


$( document ).ready(function() {
    reload_docsend_files();
 var docsend_drop = new Dropzone("#js-docsend-dropzone", { 
      paramName: "datei",
      clickable: true,
      <%*acceptedFiles: ".jpg,.jpeg,.png,.pdf,.xls,.csv",*%>
      url:"<%$eurl%>cmd=ds_file_upload",
      maxFilesize: 32 
    });
    docsend_drop.on("success", function(file,responseText) {
        docsend_drop.removeFile(file);
        var result = jQuery.parseJSON(responseText);
        if (result.status=='failed') {
            $('#dropzonefeedback').append('<p class="text-danger"><i class="fa fa-times"></i> '+result.filename+'</p>');
            show_msge(result.filename);            
        } else {
            $('#dropzonefeedback').append('<p class="text-success"><i class="fa fa-check-circle-o"></i> '+result.filename+'</p>');
        }
    });  
    docsend_drop.on("drop", function() {
         $('#js-docsend-dropzone').html('');
         $('#dropzonefeedback').html('');
         $('#dropzonefeedback').show();  
    });   
    docsend_drop.on("queuecomplete", function() {
         $('#js-docsend-dropzone').html('Drag & Drop Dateien hier');
         reload_docsend_files();        
    });   
    docsend_drop.on("error", function(file, message) { 
        alert(message);
        this.removeFile(file);           
    });      
});
</script>