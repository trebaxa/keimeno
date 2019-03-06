<div class="row">
<% foreach from=$picquickjump item=foto name=gqloop %>
    <div class="col-md-4 text-center">
        <a class="galhoverclick ajax-link" rel="<%$foto.preview%>" title="{LBL_EDIT}" href="<%$foto.edit_link%>">
		<img class="img-thumbnail" src="<% $foto.thumbnail %>" alt="<%$foto.imginfo.pic_title%>" title="<%$foto.width_foto_px%> x <%$foto.height_foto_px%>" >
		</a>			
	</div>
<% /foreach %>
</div>