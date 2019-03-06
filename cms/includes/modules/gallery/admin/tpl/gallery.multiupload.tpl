<div class="page-header"><h1><i class="fa fa-photo"><!----></i> {LBL_PICMANAGER}</h1></div>
<h3>Multi Upload</h3>


<% if ($GET.upload_finish==1) %>
<div class="alert alert-success">
    Bilder erfolgreich importiert.
</div>
<%/if%>

<% if ($section=='multiupload') %>
<div class="col-md-6">
    <fieldset>	
        <legend>{LBL_SETTINGS}</legend>
        <form method="post" onSubmit="showPageLoadInfo();" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" value="<%$epage%>" name="epage">
        <input type="hidden" name="cmd" value="mu_save_sess">
        
            <div class="form-group">
        		<label>Gallery:</label>
                <select class="form-control" name="FORM[group_id]">
                    <% foreach from=$GALADMIN.tree_select key=catid item=cname %>
                        <option value="<%$catid%>"><%$cname%></option>
                    <%/foreach%>
                </select>
        	</div>
        	<div class="form-group">
        		<label>admin. {LBL_TITLE}:</label>
                <input type="text" class="form-control" name="FORM[pic_title]" value="<%$EDITOR.FORM.pic_title|hsc%>">
        	</div>	
        	<div class="form-groupl">
        		<label>{LBLA_FOTOSOURCEURL}:</label>
                <input type="text" class="form-control" name="FORM[fotoquelle]" value="<%$EDITOR.FORM.fotoquelle|hsc%>">
        	</div>
        	<div class="form-group">
        		<label>{LBL_PUBLISHEDAT}:</label>
                <input type="text" class="form-control" name="FORM[post_time_int]" maxlength="10" value="<%$EDITOR.FORM.post_time_int|hsc%>">
                <p class="help-block">(dd.mm.YYYY)</p>
        	</div>
        
        <div class="subright"><%$nextbtn%></div>
        </form>
    </fieldset>	
</div>
<%/if%>

<% if ($section=='mu_up') %>
<div class="dropzonecss" id="js-gallery-dropzone">
    Drag & Drop Dateien hier
</div>
<div id="dropzonefeedback"></div>


<script>
$(document).ready(function() {  
    var product_pic_drop = new Dropzone("#js-gallery-dropzone", { 
      paramName: "bilddatei",
      clickable: true,
      acceptedFiles: ".jpg,.jpeg,.png",
      url:"<%$PHPSELF%>?epage=gallerypicmanager.inc&cmd=dragdropfile_gallery&gid=<%$GET.gid%>",
      maxFilesize: 9 
    });
    product_pic_drop.on("success", function(file,responseText) {
        product_pic_drop.removeFile(file);
        var result = jQuery.parseJSON(responseText);
        if (result.status=='failed') {
            $('#dropzonefeedback').append('<p class="text-danger"><i class="fa fa-times"></i> '+result.filename+'</p>');            
        } else {
            $('#dropzonefeedback').append('<p class="text-success"><i class="fa fa-check-circle-o"></i> '+result.filename+'</p>');
        }
    });  
    product_pic_drop.on("drop", function() {
         $('#js-gallery-dropzone').html('');
         $('#dropzonefeedback').show();    
    });   
    product_pic_drop.on("queuecomplete", function() {
         $('#js-gallery-dropzone').html('Drag & Drop Dateien hier');
         setTimeout("$('#dropzonefeedback').fadeOut()",3000);        
    });   
    product_pic_drop.on("error", function(file, message) { 
        show_msge(message);
        this.removeFile(file);       
    });         
});

</script> 
<%/if%>