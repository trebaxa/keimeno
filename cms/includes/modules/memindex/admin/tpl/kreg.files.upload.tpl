
<% include file="cb.panel.header.tpl" title="Dateien"%>
    <div class="dropzonecss" id="js-customer-dropzone">
            Drag & Drop Dateien hier
        </div>
        <div id="dropzonefeedback"></div>
    <div id="js-customer-files"></div>
<% include file="cb.panel.footer.tpl"%>

<script>

var current_folder='<%$MEMINDEX.root_hash%>';

function reload_customer_files() {
    simple_load('js-customer-files','<%$eurl%>cmd=reload_customer_files&kid=<%$GET.kid%>&folder='+current_folder);
}

reload_customer_files();
$(document).ready(function() {  
    var doc_drop = new Dropzone("#js-customer-dropzone", { 
      paramName: "datei",
      clickable: true,
      <%*acceptedFiles: ".jpg,.jpeg,.png,.pdf,.xls,.csv",*%>
      url:"<%$eurl%>cmd=dragdropfile_user&kid=<%$GET.kid%>&folder="+current_folder,
      maxFilesize: 9 
    });
    
    doc_drop.on("success", function(file,responseText) {
        doc_drop.removeFile(file);
        var result = jQuery.parseJSON(responseText);
        if (result.status=='failed') {
            $('#dropzonefeedback').append('<p class="text-danger"><i class="fa fa-times"></i> '+result.filename+'</p>');
            show_msge(result.filename);            
        } else {
            $('#dropzonefeedback').append('<p class="text-success"><i class="fa fa-check-circle-o"></i> '+result.filename+'</p>');
        }
    });  
    doc_drop.on("drop", function() {
         $('#js-customer-dropzone').html('');
         $('#dropzonefeedback').slideDown();    
    });   
    doc_drop.on("queuecomplete", function() {
         $('#js-customer-dropzone').html('Drag & Drop Dateien hier');
         reload_customer_files();
         $('#dropzonefeedback').slideUp();        
    });   
    doc_drop.on("error", function(file, message) { 
        show_msge(message);
        this.removeFile(file);           
    });         
});
        
</script>