<% if ($gallery_random_images) %>
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/milkbox/milkbox.css);
</style>
    
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-core.js"></script>
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-more.js"></script> 
    <script type="text/javascript" src="<% $PATH_CMS %>js/milkbox.js"></script>    
<% assign var=line_break value="3" %>
<table class="tab_std"  width="100%">
<tr class="header"><td colspan="<% $line_break %>">Fotos</td></tr>
<tr>
<% foreach from=$gallery_random_images item=foto name=gloop %>
 <td width="<% math equation="x / y" x=100 y=$line_break %>%" valign="top" align="center">
 <a title="&lt;strong&gt;<% $foto.img_title %> &lt;/strong&gt;&lt;br /&gt;<% $foto.img_description %>" rel="milkbox[<% $foto.img_galleryname %>]" href="<% $foto.img_redfullsize %>">
 <img src="<% $foto.img_src %>" ></a>
<% if ($foto.img_title!='') %> <br> <strong><% $foto.img_title %></strong> <% /if %>
<% if ($foto.img_descshort!='') %> <br> <% $foto.img_descshort%> <% /if %>

<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.edit==TRUE)) %>
        <br><a title="{LBL_EDIT}" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=edit">
        <img alt="{LBL_EDIT}" title="{LBL_EDIT}" src="<%$SSL_PATH_SHOP%><%$PATH_SHOP%>images/opt_edit.png" ></a>
<%/if%>        
<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.del==TRUE)) %>
        <a title="{LBL_DELETE}" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=delpic">
        <img alt="{LBL_DELETE}" title="{LBL_DELETE}" src="<%$SSL_PATH_SHOP%><%$PATH_SHOP%>images/opt_del.png" ></a> 
<%/if%>                
 </td>
        <% if ($smarty.foreach.gloop.iteration % $line_break == 0)%></tr><tr><%/if%>
<% /foreach %>
</tr>
</table>
<% /if %>
