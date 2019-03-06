<% if ($gallery) %>
<section>
<h2><% $GAL_OBJ.gallery_name %></h2>
  <div class="row">
  <% foreach from=$gallery item=foto key=id name=gloop %>
    <div class="col-md-4">
     <a href="<% $foto.img_redfullsize %>" title="<% $foto.img_title%><% if ($foto.img_copyright!='') %>&lt;br&gt;Quelle:<% $foto.img_copyright%><%/if%>&lt;br&gt;<% $foto.imginfo.pic_content|hsc%>">
     <img class="img-thumbnail" id="foto<%$id%>" src="<% $foto.img_src %>" alt="Fotos" /></a>
      <div class="fdesc"><p><b><% $foto.img_title%></b><br> <% $foto.imginfo.pic_content|st|truncate:150%></p></div>
    </div>
    <% if ($smarty.foreach.gloop.iteration is div by 3) %></div><div class="row"  ><%/if%>
  <% /foreach %>
  </div>
</section>
<% /if %>