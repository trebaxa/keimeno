<link rel="stylesheet" href="../includes/modules/gallery/admin/css/style-1.css">
<% if ($cmd=="load_groups") %>
<div class="page-header"><h1> <i class="fa fa-photo"><!----></i> {LBL_FOTOALBUMS}</h1></div>


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">	
                    <%include file="gallery.grouptree.tpl"%>
    	</div>
        <div class="col-sm-9 col-md-10 main" id="gallerygrouplist">
    		  <%include file="gallery.grouplist.tpl"%>
    	</div>      
    </div>
</div>
<%/if%>


<!-- Modal -->
<div class="modal fade" id="addalbum" tabindex="-1" role="dialog" aria-labelledby="addalbumLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form method="post" action="<%$PHPSELF%>" class="jsonform">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="addalbumLabel">{LBL_ADD}:</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>{LBL_FOTOALBUM}:</label>
            <%$GALADMIN.parentselect%>
        </div>
        <div class="form-group">
	       <label>admin. {LBL_TITLE}:</label>
           <input type="text" class="form-control"  name="FORM[groupname]" value="">
        </div>   
	   <input type="hidden" name="cmd" value="add_gallery_group"/>
       <input type="hidden" value="<%$epage%>" name="epage"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      	</form>
    </div>
  </div>
</div>

<% if ($cmd=="edit_group") %>
    <%include file="gallery.groupedit.tpl"%>
<% /if %>

<script>
function reload_gallery_table(gid) {
    simple_load('gallerygrouplist','<%$eurl%>cmd=load_group_table&gid='+gid);
}
</script>