<% if ($GET.gid>0) %>
<style>

.dropzonecss {
    min-height: 100px;
    padding: 3px;
    line-height: 80px;
}
</style>
<div class="row mt-lg">
 <div class="col-md-6">
   <div class="js-gallery-title"></div>   
 </div>
 <div class="col-md-6">
        <div class="dropzonecss" id="js-gallery-dropzone">
            Drag & Drop Dateien hier
        </div>
        <div id="dropzonefeedback"></div>
   </div>  
   </div> 
</div>


<script>
$(document).ready(function() {  
    var product_pic_drop = new Dropzone("#js-gallery-dropzone", { 
      paramName: "bilddatei",
      clickable: true,
      acceptedFiles: ".jpg,.jpeg,.png",
      url:"<%$PHPSELF%>?epage=gallerypicmanager.inc&cmd=dragdropfile_gallery&gid=<%$GALADMIN.gid%>",
      maxFilesize: 9 
    });
    product_pic_drop.on("success", function(file,responseText) {
        product_pic_drop.removeFile(file);
        var result = jQuery.parseJSON(responseText);
        if (result.status=='failed') {
            $('#dropzonefeedback').append('<p class="text-danger"><i class="fa fa-times"></i> '+result.filename+'</p>');
            show_msge(result.filename);            
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
         reloadfotos(<%$GALADMIN.gid%>);        
    });   
    product_pic_drop.on("error", function(file, message) { 
        show_msge(message);
        this.removeFile(file);           
    });         
});


    $('.js-gallery-title').html('<h3><%$POBJ.galinfo.groupname%></h3>Anzahl: <span class="badge badge-info"><%$GALADMIN.galtab|@count%></span>');
    $('.js-gallery-info').html('Anzahl: <%$POBJ.galinfo.pic_count_gallery%>&nbsp;|&nbsp;belegt:<% $POBJ.galinfo.totalsizekb %>&nbsp;|&nbsp;Speicher:<% $POBJ.galinfo.gblsizekb %>');
   
</script>
<div class="row form-inline mb-lg">
    <div class="col-md-6">
    <form class="form-inline">
	 <div class="form-group">
        <label>Filter:</label>	
        <select class="form-control custom-select js-filtergal">
    		 <option <% if ($GALADMIN.cs_gal.approved==2) %>selected<%/if%> value="<%$eurl%>cmd=load_pics&section=<%$section%>&gid=<%$GALADMIN.gid%>&cs_filter=2">alle Bilder</option>
    		 <option <% if ($GALADMIN.cs_gal.approved==0) %>selected<%/if%> value="<%$eurl%>cmd=load_pics&section=<%$section%>&gid=<%$GALADMIN.gid%>&cs_filter=0">nicht genehmigte</option>
    		 <option <% if ($GALADMIN.cs_gal.approved==1) %>selected<%/if%> value="<%$eurl%>cmd=load_pics&section=<%$section%>&gid=<%$GALADMIN.gid%>&cs_filter=1">genehmigte</option>
		</select>
      </div>
     </form>   
    </div>
    <div class="col-md-6 text-right">
    <% if ($POBJ.galinfo.piccount>0) %>
		<form method="post" class="form-inline" action="<%$PHPSELF%>" enctype="multipart/form-data">
			<input type="hidden" name="gid" value="<%$GALADMIN.gid%>">
			<input type="hidden" value="<%$epage%>" name="epage">
			<input type="hidden" name="aktion" value="a_sort">
            <div class="form-group">            
            <label class="sr-only">Sortierung</label>
            <select class="form-control custom-select" name="column">
    			<option value="pic_title">{LBL_SORTALPHA}</option>
    			<option value="post_time_int">{LBL_DATE}</option>
    			<option value="pic_size">{LBL_FILESIZE}</option>
			</select>
            </div>
            <div class="form-group">
                <label class="sr-only">Sort. Richtung</label>
                <select class="form-control custom-select" name="direction">
        			<option value="DESC">{LBL_DESC}</option>
        			<option value="ASC">{LBL_ASC}</option>
    			</select>
            </div>
            <%$subbtn%>	
		</form>
        <%/if%>
        </div>
</div>
<script>
$( ".js-filtergal" ).change(function() {
    simple_load('js-fotolist',$(this).val());
});
</script>
<style>
#fototable h3 {
    font-size:99%;
    display:block;
    text-align:center;
}

</style>
<% if (count($GALADMIN.galtab)>0) %>
		<div class="row" id="fototable">
        	<% foreach from=$GALADMIN.galtab item=foto name=gloop %>
        <div   class="col-md-3" id="pic-cont-<%$foto.imginfo.PICID%>">
        <div class="card" style="height:auto;">
            <a title="{LBL_RESOLUTION}:<%$foto.width_foto_px%> x <%$foto.height_foto_px%>
            <br>{LBL_FILESIZE}: <% $foto.filesize %>
            <br>{LBL_PUBLISHEDAT}: 	<% $foto.img_posttime %>            
            " data-toggle="tooltip" data-placement="bottom" rel="<%$foto.preview%>" title="{LBL_EDIT}" onclick="showPageLoadInfo();" href="<%$foto.edit_link%>">
				<img class="card-img-top" src="<% $foto.thumbnail %>" alt="<%$foto.imginfo.pic_title|sthsc%>" >
				</a>
            
            <div class="card-body wordbreak text-center">
                <h5 class="card-title"><% if ($foto.imginfo.pic_title!="") %><% $foto.imginfo.pic_title|truncate:60 %><%else%>-<%/if%></h5>
            
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="btn-group">
             <%$foto.icon_edit%>
             <a href="javascript:void(0)" class="btn btn-secondary text-danger picdelete" rel="<%$foto.imginfo.PICID%>"><i class="fa fa-trash"><!----></i></a>
             <%$foto.icon_approve%>
             <% if ($GALADMIN.flickractive==true) %>
                <a href="javascript:void(0)" class="btn btn-secondary flickr" data-tags="<%$foto.imginfo.pic_tags%>" data-picid="<%$foto.imginfo.PICID%>" data-title="<%$foto.imginfo.pic_title|hsc%>" data-description="<%$foto.imginfo.pic_content|st|hsc%>"><i class="fa fa-flickr fa-pink"><!----></i></a>
             <%/if%>	
			        
            </div>        
        </div>
    </div>
    <div class="row">    
        <div class="col-md-12">
				<table class="table table-condensed" >
				<tr>
				<td >{LBL_SORTING}:</td>
				<td class="text-right">				
					<input title="{LBL_SORTING}" type="text" class="form-control" name="morder[<%$foto.imginfo.PICID%>]" value="<%$foto.imginfo.morder|hsc%>" size="3">
				</td>
            </tr>
            <tr>
                <td>Markieren:</td>
                <td> <input title="{LBL_SELECT}" type="checkbox" name="metaids[]" value="<%$foto.imginfo.PICID%>">
					<% if ($foto.imginfo.approved==1) %>
						<input <% if ($POBJ.album_picid==$foto.imginfo.PICID) %>checked<%/if%> title="{LBL_ALBUMFOTO}" type="radio" name="albumtitlepicid" value="<%$foto.imginfo.PICID%>">
					<%/if%></td>
            </tr>
				</table>	
			</div> <!-- col -->
           </div> <!-- row -->
            </div>
            </div></div>
            <% /foreach %>
	</div>

<table class="table table-striped table-hover"  >
	<tr>
		<td><% if ($POBJ.galinfo.piccount>0) %> 
        Aktion:<select class="form-control custom-select" name="cmd">
		<option value="a_msave">speichern</option>
		<option value="a_deletem">markierte l&ouml;schen</option>
		<option value="a_app">markierte ver&ouml;ffentlichen</option>
		<option value="a_disapp">markierte nicht ver&ouml;ffentlichen</option>
		<option value="a_movecat">markierte bewegen</option>
		</select>... nach Album: 
        <select class="form-control custom-select" name="FORM[group_id]">
            <% foreach from=$GALADMIN.tree_select key=catid item=cname %>
                <option value="<%$catid%>"><%$cname%></option>
            <%/foreach%>
        </select>
        <input type="hidden" name="gid" value="<%$GALADMIN.gid%>">
        <% $GALADMIN.gobtn %>
		<%/if%> </td>
	
	</tr>
</table>  
<%else%>
<div class="alert alert-info">Keine Bilder vorhanden</div>
<%/if%>
<div class="text-right"><small>ID: <%$GET.gid%></small></div>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip({html: true})
})
set_ajaxapprove_icons();
set_ajaxdelete_icons('{LBL_CONFIRM}', '<%$epage%>');

$('#fototable .btn').addClass('btn-sm');

$('.picdelete').css('cursor','pointer');
$('.picdelete').unbind('click');
$(".picdelete").click(function () {
    var id = $(this).attr('rel');
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=axdelpic&id=' + id);    
    $('#pic-cont-'+id).fadeTo(400, 0, function () { 
        $(this).remove();
    });
    return false;
});
</script>


<script>
<% if ($GALADMIN.flickractive==true) %>
$('.flickr').css('cursor','pointer');
$('.flickr').unbind('click');
$(".flickr").click(function () {
    $('#fl_picid').val($(this).data('picid'));
    $('#fl_title').val($(this).data('title'));
    $('#fl_tags').val($(this).data('tags'));
    $('#fl_description').val($(this).data('description'));
    $('#flickruploader').modal('show');
});

function flickrsend() {
  $('#flickruploader').modal('hide');
}
</script>
<%/if%>
<%/if%>
  