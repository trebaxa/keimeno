<% if ($cmd=='showfoto') %>
<h1><%$loaded_foto.img_title%></h1>
<img src="<%$loaded_foto.img_redfullsize%>">
<p><%$loaded_foto.img_description|st%></p>

<%else%>

<% include file="gallery_customer_upload.tpl"%>
<h1><% $GAL_OBJ.gallery_name %></h1>
<ol class="breadcrumb"><li><a href="{URL_TPL_3}">Start</a></li><% $gallery_obj.gallery_breadcrumbs %></ol>
<% if ($gallery) %>
<% include file="paging.tpl" %>
<% include file=$GAL_OBJ.tpl_name %>
<% /if %>


<% if ($subgal) %>
<h2>{LBL_SUBGAL} <% $GAL_OBJ.gallery_name %> </h2>
<h3>Galerie</h3>
<div class="row">
<% foreach from=$subgal item=sg_obj name=sg %>
 <div class="col-md-6 text-center">
    <% if ($sg_obj.subgal_img!="") %>
        <a title="<% $sg_obj.subgal_title %>" href="<% $sg_obj.subgal_link %>">
            <img class="img-thumbnail" src="<% $sg_obj.subgal_imgsrc %>" ><br><%/if%><% $sg_obj.subgal_title %>
            <% if ($sg_obj.subgal_fotocount>0) %>(<% $sg_obj.subgal_fotocount %>)<% /if %>
        </a>
    </div>
<% /foreach %>
</div>
<% /if %>

<% if ($galleries) %>
<h2>{LBL_MOREGALLERIES}</h2>
<h2>{LBL_SUBGAL} <% $GAL_OBJ.gallery_name %> </h2>
<h3>Galerie</h3>
<div class="row">
<% foreach from=$galleries item=sg_obj name=sg %>
 <div class="col-md-6 text-center">
    <% if ($sg_obj.subgal_imgsrc !="") %> 
        <a title="<% $sg_obj.subgal_title %>" href="<% $sg_obj.subgal_link %>">
        <img src="<% $sg_obj.subgal_imgsrc %>"  class="img-thumbnail"></a><br> 
    <%/if%>
<a title="<% $sg_obj.subgal_title %>" href="<% $sg_obj.subgal_link %>"><% $sg_obj.subgal_title %>
<% if ($sg_obj.subgal_fotocount>0) %>(<% $sg_obj.subgal_fotocount %>)<% /if %>
</a>
 </div>
<% /foreach %>
</div>
<% /if %>
<%/if%>