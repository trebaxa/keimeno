<% if ($gallery_latest_images) %>
<div class="box">
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/milkbox/milkbox.css);
</style>
    
    
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-more.js"></script> 
    <script type="text/javascript" src="<% $PATH_CMS %>js/milkbox.js"></script>    
<% assign var=line_break value="3" %>
<h1>Neueste Fotos</h1>
<table class="tab_std"  width="100%">
<tr>
<% foreach from=$gallery_latest_images item=foto name=gloop %>
 <td width="<% math equation="x / y" x=100 y=$line_break %>%" valign="top" style="text-align:center">
 <a title="&lt;strong&gt;<% $foto.img_title %> &lt;/strong&gt;&lt;br /&gt;<% $foto.img_descriptionplain|hsc %><% if ($foto.img_copyright!='') %>&lt;br&gt;Quelle:<% $foto.img_copyright%><%/if%>" rel="milkbox[startsite]" href="<% $foto.img_redfullsize %>">
 <img alt="<%$foto.img_title|hsc%>" src="<% $foto.img_src %>" ></a>
<% if ($foto.img_title!='') %> <br> <strong><% $foto.img_title|truncate:30 %></strong> <% /if %>
<% if ($foto.img_descshort!='') %> <br><span class="small"><% $foto.img_descshort|truncate:50%></span> <% /if %>
<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.edit==TRUE)) %>
        <br><a title="bearbeiten" href="<%$PHPSELF%>?page=3&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=edit">
        <img alt="bearbeiten" title="bearbeiten" src="<%$PATH_CMS%>images/opt_edit.png" ></a>
<%/if%>        
<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.del==TRUE)) %>
        <a title="lÃ¶schen" href="<%$PHPSELF%>?page=3&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=delpic">
        <img alt="lÃ¶schen" title="lÃ¶schen" src="<%$PATH_CMS%>images/opt_del.png" ></a> 
<%/if%>                
 </td>
        <% if ($smarty.foreach.gloop.iteration % $line_break == 0 || $smarty.foreach.gloop.last==TRUE)%></tr><tr><%/if%>
<% /foreach %>
</tr>
</table>
</div>
<% /if %>
