<div class="form-group">
    <label>Galerieauswahl:</label>
    <select class="form-control custom-select" name="PLUGFORM[galleryid]" id="js-gallery-group-id">
     <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
        <option <% if ($WEBSITE.node.tm_plugform.galleryid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
     <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tpl_name]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tpl_name==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>Galerie Themen-Bild Breite:</label>
        <input required="" maxlength="4" type="text" class="form-control" name="PLUGFORM[gal_image_width]" value="<% $WEBSITE.node.tm_plugform.gal_image_width|sthsc %>">
    </div>
    
    <div class="form-group col-md-6">
        <label>Galerie Themen-Bild Bild Icon H&ouml;he:</label>
        <input required="" maxlength="4" type="text" class="form-control" name="PLUGFORM[gal_image_height]" value="<% $WEBSITE.node.tm_plugform.gal_image_height|sthsc %>">
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>Galerie Themen-Bild Methode:</label>
        <select class="form-control custom-select" name="PLUGFORM[gal_thumb_type]">        
                <option <% if ($WEBSITE.node.tm_plugform.gal_thumb_type=='crop') %>selected<%/if%> value="crop">zuschneiden (crop)</option>        
                <option <% if ($WEBSITE.node.tm_plugform.gal_thumb_type=='resize') %>selected<%/if%> value="resize">verkleinern (resize)</option>
                <option <% if ($WEBSITE.node.tm_plugform.gal_thumb_type=='resizetofit') %>selected<%/if%> value="resizetofit">verkleinern (fit)</option>
                <option <% if ($WEBSITE.node.tm_plugform.gal_thumb_type=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">verkleinern (fit PNG)</option>
                <option <% if ($WEBSITE.node.tm_plugform.gal_thumb_type=='boxed') %>selected<%/if%> value="boxed">boxed</option>            
        </select>
    </div>
    
    <div class="form-group col-md-6">
        <label>Galerie Themen-Bild Crop Position:</label>    
        <select class="form-control custom-select" name="PLUGFORM[gal_g_croppos]" >
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='NorthWest') %>selected<%/if%> value="NorthWest">NorthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='North') %>selected<%/if%> value="North">North</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='NorthEast') %>selected<%/if%> value="NorthEast">NorthEast</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='West') %>selected<%/if%> value="West">West</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='Center') %>selected<%/if%> value="Center">Center</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='East') %>selected<%/if%> value="East">East</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='SouthWest') %>selected<%/if%> value="SouthWest">SouthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='South') %>selected<%/if%> value="South">South</option>
            <option <% if ($WEBSITE.node.tm_plugform.gal_g_croppos=='SouthEast') %>selected<%/if%> value="SouthEast">SouthEast</option>
        </select>  
    </div>
</div>

<div class="row">
    <div class="form-group col-md-4">
        <label>Bilder Anzahl:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[image_count]" value="<% $WEBSITE.node.tm_plugform.image_count %>">
    </div>

    <div class="form-group col-md-4">
        <label>Thumb Breite:</label>
        <input maxlength="4" type="text" class="form-control" name="PLUGFORM[image_width]" value="<% $WEBSITE.node.tm_plugform.image_width|sthsc %>">
    </div>
    
    <div class="form-group col-md-4">
        <label>Thumb Icon H&ouml;he:</label>
        <input maxlength="4" type="text" class="form-control" name="PLUGFORM[image_height]" value="<% $WEBSITE.node.tm_plugform.image_height|sthsc %>">
    </div>
</div>    

<div class="row">
    <div class="form-group col-md-6">
        <label>Methode:</label>
        <select class="form-control custom-select" name="PLUGFORM[thumb_type]">        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='crop') %>selected<%/if%> value="crop">zuschneiden (crop)</option>        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resize') %>selected<%/if%> value="resize">verkleinern (resize)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofit') %>selected<%/if%> value="resizetofit">verkleinern (fit)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">verkleinern (fit PNG)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='boxed') %>selected<%/if%> value="boxed">boxed</option>            
        </select>
    </div>
    
    <div class="form-group col-md-6">
        <label>Crop Position:</label>    
        <select class="form-control custom-select" name="PLUGFORM[g_croppos]" >
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='NorthWest') %>selected<%/if%> value="NorthWest">NorthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='North') %>selected<%/if%> value="North">North</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='NorthEast') %>selected<%/if%> value="NorthEast">NorthEast</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='West') %>selected<%/if%> value="West">West</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='Center') %>selected<%/if%> value="Center">Center</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='East') %>selected<%/if%> value="East">East</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='SouthWest') %>selected<%/if%> value="SouthWest">SouthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='South') %>selected<%/if%> value="South">South</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='SouthEast') %>selected<%/if%> value="SouthEast">SouthEast</option>
        </select>  
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>Sortierung:</label>   
        <select class="form-control custom-select" name="PLUGFORM[default_order]" >
            <option <% if ($WEBSITE.node.tm_plugform.default_order=='post_time_int') %>selected<%/if%> value="post_time_int">Upload Datum</option>
            <option <% if ($WEBSITE.node.tm_plugform.default_order=='pic_title') %>selected<%/if%> value="pic_title">Titel</option>
            <option <% if ($WEBSITE.node.tm_plugform.default_order=='morder') %>selected<%/if%> value="morder">Manuelle Sortierng</option>
        </select>
    </div>
          
    <div class="form-group col-md-6">
        <label>Sortierung Richtung:</label> 
        <select class="form-control custom-select" name="PLUGFORM[default_direc]" >
            <option <% if ($WEBSITE.node.tm_plugform.default_direc=='ASC') %>selected<%/if%> value="ASC">aufsteigend</option>
            <option <% if ($WEBSITE.node.tm_plugform.default_direc=='DESC') %>selected<%/if%> value="DESC">absteigend</option>
        </select>
    </div>
</div>



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
      url:"<%$PHPSELF%>?epage=gallerypicmanager.inc&cmd=dragdropfile_gallery&gid="+$('#js-gallery-group-id').val(),
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