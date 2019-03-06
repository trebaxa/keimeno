<link rel="stylesheet" href="../includes/modules/gallery/admin/css/style.css">
<div class="page-header"><h1><i class="fa fa-photo"><!----></i> {LBL_PICMANAGER}</h1></div>
<%*<div class="btn-group">
    <a class="btn btn-default" href="javascript:void(0)" onclick="$('#js-fotoupl-galgroup').val('<%$GALADMIN.gid%>')" data-toggle="modal" data-target="#galfotoup">Foto Upload</a>
</div>
*%>
<%include file="gallery.fotoupload.tpl"%>

<% if ($GALADMIN.gid==0) %><div class="bg-info text-info">	{LBL_PLEASECHOOSEGAL}</div><%/if%>
<% if ($GALADMIN.albumcount==0) %><div class="bg-info text-info">{LBL_ADDGAL}</div><%/if%>
 
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" class="jsonform form-inline">
    <input type="hidden" value="<%$epage%>" name="epage">
    <input type="hidden" value="<%$section%>" name="section">
    <div class="x_panel">
      <div class="x_title">
         <h2>Gallery <span class="js-gallery-title"></span></h2>
         <div class="clearfix"></div>
      </div>
          <div class="x_content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-3 col-md-2 sidebar">	
                    		<div id="adminmenu">
                    		      <%include file="gallery.tree.tpl"%>
                    		</div>
                        </div>
                        <div class="col-sm-9 col-md-10 main" id="galleryrightcont">
                    		  <div id="js-fotolist"></div>
                        </div>      
                    </div>
                </div>
          </div>
          <div class="panel-footer js-gallery-info">
          
          </div>
    </div>

</form>

<script>


function reloadfotos(gid) {
    simple_load('js-fotolist','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_pics&gid='+gid);
}
    simple_load('js-fotolist','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_pics&gid=<%$GALADMIN.gid%>');
    $('.mktree a').removeClass('active');
    $('#treeitem-<%$GALADMIN.gid%>').addClass('active');    
</script>


<% if ($GALADMIN.flickractive==true) %>
<!-- Modal -->
<div class="modal fade" id="flickruploader" tabindex="-1" role="dialog" aria-labelledby="flickruploaderLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="POST" class="jsonform form">
    <input type="hidden" name="epage" value="<%$epage%>" >
    <input type="hidden" name="cmd" value="post_to_flickr" >
    <input type="hidden" name="FORM[picid]" id="fl_picid" value="post_to_flickr" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="flickruploaderLabel">Flickr Upload</h4>
      </div>
      <div class="modal-body">

    <div class="form-group">
        <label>Titel:</label>
        <input type="text" class="form-control" required name="FORM[title]" id="fl_title" value="">
    </div>  
    <div class="form-group">
        <label>Beschreibung:</label>
        <input type="text" class="form-control" required name="FORM[description]" id="fl_description" value="">
    </div>   
    <div class="form-group">
        <label>Tags:</label>
        <input type="text" class="form-control" required name="FORM[tags]" id="fl_tags" value="">
    </div>   
    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$sendbtn%>
      </div>
      </form>
    </div>
  </div>
</div>

<%/if%>