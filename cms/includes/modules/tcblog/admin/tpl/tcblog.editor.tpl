   
<div id="topofpage">
    <%include file="cb.panel.header.tpl" title="`$TCBLOG.FORM_CON.title`"%>
   <div class="row">
    <div class="col-md-12 text-right">
        <%$TCBLOG.blogitem.icon_approve%>
    </div>
</div>   
    <% if ($GBLPAGE.access.language==TRUE)%>
        <form <% if ($cmd=='edit') %>class="jsonform"<%/if%> action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
        <%$TCBLOG.blogitem.langselect%>
        <input type="hidden" name="epage" value="<%$epage%>">
	         <input type="hidden" name="FORM[group_id]" value="<%$TCBLOG.blog_group_id%>">
							<input type="hidden" name="conid" value="<%$TCBLOG.FORM_CON.id%>">
							<input type="hidden" name="id" value="<%$GET.id%>">
							<input type="hidden" name="FORM_CON[lang_id]" value="<%$GET.uselang%>">
							<input type="hidden" name="cmd" value="save_item">
                           
<div class="row">
    <div class="col-md-6">
		<div class="form-group">
            <label>{LBL_TITLE}:</label>
            <input class="form-control" size="40" type="text" value="<%$TCBLOG.FORM_CON.title|sthsc%>" name="FORM_CON[title]">
        </div>
		<div class="form-group">
            <label>{LBL_INTRODUCTION}:</label>
            <textarea class="form-control" rows="6" name="FORM_CON[introduction]"><%$TCBLOG.FORM_CON.introduction|sthsc%></textarea>
        </div>        
        <div class="form-group">
            <label>{LBL_DATE}:</label>
            <input  type="text" class="form-control" value="<%$TCBLOG.blogitem.ndate|sthsc%>" name="FORM[ndate]">
        </div>
        <div class="form-group">
            <label>Tags:</label>
            <input  type="text" class="form-control" value="<%$TCBLOG.blogitem.tags|sthsc%>" name="FORM[tags]">
        </div>        
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Author:</label>
            <input type="text" class="form-control" value="<%$TCBLOG.blogitem.username|sthsc%>" name="FORM[username]">
        </div>        
        <div class="form-group">
            <label>YouTube Video ID:</label>
            <input style="width:160px" type="text" class="form-control" value="<%$TCBLOG.FORM_CON.b_ytid|sthsc%>" name="FORM_CON[b_ytid]">
        </div>
        <div class="form-group">
        <label for="datei"></label>
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name="">
                <input id="datei" class="xform-control autosubmit" type="file" onchange="this.previousElementSibling.value = this.value" value="" name="datei">
                <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
            </div>
            <br><i class="fa fa-trash del_blog_img" style="display:none;cursor:pointer;position:absolute;margin-left:10px;margin-top:10px;"></i>            
            <img src="./images/axloader.gif" id="blog-theme-img" style="display:none;" class="img-thumbnail">        
        </div>
		
    </div>
</div>    
    <div class="form-group">
            <label>{LBL_CONTENT}:</label>
            <%$TCBLOG.blogitem.editor%>
    </div>
        <%$subbtn%></form>
   
   <div id="videofoto" class="row">
        <div class="col-md-6">
        <label>Foto Galerie zum Blog Beitrag</label>
        <form action="<%$PHPSELF%>" method="POST" id="videoform" class="jsonform" enctype="multipart/form-data">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="save_blog_foto">
            <input type="hidden" name="id" value="<%$TCBLOG.FORM_CON.id%>">
            <div class="form-group">
            <label for="datei-gal"></label>
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
                    <input id="datei-gal" class="xform-control autosubmit" type="file" onchange="this.previousElementSibling.value = this.value" value="" name="datei"></input>
                    <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                    <input onclick="toggle_off();" type="submit" id="fmr-vfoto-btn" class="btn btn-primary" value="upload" style="display:none">
                </div>                    
            </div>
            
        </form>
        </div>
    </div><!--row -->
    
    <div class="row">
        <div id="fotolist" class="col-md-12"></div>
    </div>  
    
    <% if (count($TCBLOG.blogitem.comments)>0) %>
     <table class="table table-hover table-striped">
      <tbody>
          <% foreach from=$TCBLOG.blogitem.comments item=row %>
            <tr>
              <td><%$row.c_autor%></td>
              <td><%$row.c_time|date_format:"%d:%m:%Y"%></td>        
              <td><%$row.c_comment|sthsc%></td>
              <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon %><%$picon%><%/foreach%></div></td>
            </tr>
          <%/foreach%>
        </tbody>
     </table> 
    <%/if%>  
    
      <%include file="cb.panel.footer.tpl"%>
</div> <!-- end top_of_page-->
    
    <script>
    function reload_blog_fotos() {
        simple_load('fotolist','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_blog_fotos&id=<%$TCBLOG.FORM_CON.id%>');
    }
    reload_blog_fotos();

    </script>

<%/if%>

<script>

function reload_item() {
    $.getJSON( "<%$PHPSELF%>?epage=<%$epage%>&cmd=load_item_json&id=<%$TCBLOG.FORM_CON.id%>", function( data ) {
        $('#blog-theme-img').attr('src','../file_data/tcblog/' + data.b_image+'?a='+Math.random());
        $('#blog-theme-img').fadeIn();
        $('.del_blog_img').show();
    }); 
}

$( ".del_blog_img" ).click(function() {
    $(this).next().fadeOut();
    $(this).hide();
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=del_img&id=<%$TCBLOG.FORM_CON.id%>');
});

 <% if ($TCBLOG.FORM_CON.b_image!="") %> reload_item(); <%/if%>
</script>        
