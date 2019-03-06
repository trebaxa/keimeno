<% if ($gallery) %>
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/gallery/prettyPhoto_compressed/css/prettyPhoto.css);
</style>
<script type="text/javascript" src="<% $PATH_CMS %>js/gallery/prettyPhoto_compressed/js/jquery.prettyPhoto.js"></script>

<% foreach from=$gallery item=foto name=gloop %>

<div style="display:inline-block; margin-right:3px; margin-left:3px;">

<a href="<% $foto.img_redfullsize %>" rel="prettyPhoto[<% $foto.img_galleryname %>]" title="&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;<% $GAL_OBJ.GALDESC%><% if ($foto.img_copyright!='') %>&lt;br&gt;Quelle:<% $foto.img_copyright%><%/if%>">
<img class="gal-border" src="<% $foto.img_src %>" >
</a>

<% if ($foto.img_title!='') %><br><strong><% $foto.img_title %></strong> <% /if %>
<% if ($foto.img_descshort!='') %><br><span class="small"> <% $foto.img_descshort|truncate:50%> </span><% /if %>
<% if ($foto.img_copyright!='') %><br>Quelle:<span class="small"><% $foto.img_copyright%></span><%/if%>

<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.edit==TRUE)) %>
        <br><a title="bearbeiten" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=edit">
        <img alt="bearbeiten" title="bearbeiten" src="<%$PATH_CMS%>images/opt_edit.png" ></a>
<%/if%>        

<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.del==TRUE)) %>
        <a title="löschen" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=delpic">
        <img alt="löschen" title="löschen" src="<%$PATH_CMS%>images/opt_del.png" ></a> 
<%/if%>  
</div>
<% if ($smarty.foreach.gloop.iteration % $gbl_config.gal_maxpicsrow == 0 && $smarty.foreach.gloop.iteration < count($gallery) )%><div class="clear"></div><%/if%>



<% /foreach %>



<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto({
    default_width: 900,
    default_height: 600
    });
  });
</script>
<% /if %>
