<div class="page-header">
    <h1>{LBLA_GAL_EDITPIC}<small>[<%$EDITOR.box_header%>]</small></h1>
</div>
<div class="btn-group">
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="$('#js-fotoupl-galgroup').val('<%$EDITOR.gid%>')" data-toggle="modal" data-target="#galfotoup">Neues Foto hochladen</a>
    <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=gallerypicmanager.inc&cmd=multiupload&section=multiupload&gid=<%$REQUSET.gid%>">Multi-Upload</a> 
    <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=initpicman&section=start&gid=<%$EDITOR.FORM.group_id%>">zu den Bildern</a>
</div>
<%include file="gallery.fotoupload.tpl"%>
<%include file="cb.panel.header.tpl" title="{LBLA_GAL_EDITPIC}"%>
<div class="row">
    <div class="col-md-6">
        <legend>{LBL_SETTINGS}</legend>
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" class="jsonform form" id="fotoupload">
            <input type="hidden" value="<%$epage%>" name="epage">
            <input type="hidden" name="id" value="<%$EDITOR.pic_id%>">
            <input type="hidden" name="gid" value="<%$EDITOR.gid%>">
            <input type="hidden" name="cmd" value="updatepic">

	<div class="row">
    <div class="col-md-6">
    
    <div class="form-group">	
        <label class="sr-only">Image:</label>	
		<% if ($EDITOR.img_src!="") %>		 
         <a href="<%$EDITOR.img_hover%>" class="galhover"><img id="picsrc" class="img-thumbnail" src="<%$EDITOR.img_src%>" alt="<%$EDITOR.FORM.pic_title|sthsc%>" /></a>          
		<%/if%>
	</div>
    </div>
    <div class="col-md-6">
        <div class="form-group">	
            <label>{LBL_RESOLUTION}:</label>
            <%$EDITOR.FORM.width_foto_px%> x <%$EDITOR.FORM.height_foto_px%>
        </div>
        <div class="form-group">	
            <label>Ansehen:</label>
            <a target="_picpreview" title="anschauen" href="../images/gallery/<%$EDITOR.FORM.pic_name%>"><%$EDITOR.FORM.pic_name%></a>
        </div>     
    </div>
    </div>
    
	<div class="form-group">
		<label>Gallery:</label>
        <select class="form-control custom-select" id="groupid" name="FORM[group_id]">
            <% foreach from=$GALADMIN.tree_select key=catid item=cname %>
                <option <% if ($EDITOR.FORM.group_id==$catid) %>selected<%/if%> value="<%$catid%>"><%$cname%></option>
            <%/foreach%>
        </select>
	</div>
	<div class="form-group">
            <label for="datei"></label>
            <div class="input-group">
            <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
            <input id="datei" class="xform-control"  type="file" name="attfile" accept="gif|jpg|png|jpeg" onchange="$('#fotoupload input:submit').click()"></input>
           <!-- <input id="datei" class="xform-control" type="file" onchange="this.previousSibling.value = this.value" value="" name="datei"></input> -->
            <span class="input-group-btn">
                <button class="btn btn-secondary" type="button">Durchsuchen</button>
            </span>

        </div>
	</div>
	<div class="form-group">
		<label>Screenshot URL:</label>
		<input type="text" class="form-control" name="FORM[pic_scrurl]" placeholder="http://www..." value="<%$EDITOR.FORM.pic_scrurl|sthsc%>">
	</div>      
	<div class="form-group">
		<label>admin. {LBL_TITLE}:</label>
        <input type="text" class="form-control"  name="FORM[pic_title]" value="<%$EDITOR.FORM.pic_title|sthsc%>">
	</div>	
	<div class="form-group">
		<label>{LBL_PUBLISHEDAT}:</label>
        <input type="date" class="form-control"  name="FORM[post_time_int]" maxlength="10" value="<%$EDITOR.FORM.post_time_int|hsc%>">(dd.mm.YYYY)
	</div>		
	
	<div class="form-group">
		<label>{LBLA_FOTOSOURCEURL}:</label>
        <input type="text" class="form-control"  name="FORM[fotoquelle]" value="<%$EDITOR.FORM.fotoquelle|hsc%>">
	</div>
	<div class="form-group">
		<label>Hash Tags:</label>
        <input type="text" class="form-control"  name="FORM[pic_tags]" value="<%$EDITOR.FORM.pic_tags|hsc%>">
	</div> 

    <%$subbtn%>
    <% if ($EDITOR.s_aktion!='a_upload' && $EDITOR.uselang>0) %>
        <legend>{LBL_DESCRIPTION}</legend>		
		<%$ADMIN.langselect%> 
		<div class="form-group">
            <label>{LBL_TITLE}:</label>
            <input type="text" class="form-control" name="FORM_CON[pic_title]"  value="<%$EDITOR.FORM_CON.pic_title|st|hsc%>">
        </div>    
		<div class="form-group">
            <label>Foto {LBLA_DESCRIPTION}:</label>
            <%$EDITOR.fck%>
        </div>    
		<input type="hidden" name="FORM_CON_ID" value="<%$EDITOR.FORM_CON.id%>">		
		<input type="hidden" name="FORM_CON[lang_id]" value="<%$EDITOR.uselang%>">
		<input type="hidden" name="uselang" value="<%$EDITOR.uselang%>">
		<input type="hidden" name="FORM_CON[pic_id]" value="<%$EDITOR.id%>">
		<%$subbtn%>
    <%/if%>
    </form>	

    </div><!--col-->
    <div class="col-md-6">
        <legend>Schnellauswahl</legend>	
        <div class="gallery_scroller" id="quickfotos"></div>
    </div><!--col-->
</div><!--row-->
<%include file="cb.panel.footer.tpl"%>
<script>
function reloadquickfotos() {
     simple_load('quickfotos','<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadquickfotos&gid='+$('#groupid').val());
     reloadpic();
}
reloadquickfotos();

function reloadpic() {
$.getJSON( '<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_foto&id=<%$EDITOR.pic_id%>', function( data ) {
    $('#picsrc').attr('src',data.img_src);  
    $('#picsrc').parent().attr('href',data.img_hover);
});
}

</script>