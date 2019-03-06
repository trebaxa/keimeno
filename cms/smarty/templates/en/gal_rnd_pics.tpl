<% if ($gallery_random_images) %>

<% assign var=line_break value="3" %>
<table class="table table-striped">
  <thead>
    <tr>
      <th colspan="<% $line_break %>">Fotos</th>
    </tr>
  </thead>
<tr>
<% foreach from=$gallery_random_images item=foto name=gloop %>
 <td width="<% math equation="x / y" x=100 y=$line_break %>%" valign="top" align="center">
 <a title="&lt;strong&gt;<% $foto.img_title %> &lt;/strong&gt;&lt;br /&gt;<% $foto.img_description %>" href="<% $foto.img_redfullsize %>">
 <img src="<% $foto.img_src %>" ></a>
<% if ($foto.img_title!='') %> <br> <strong><% $foto.img_title %></strong> <% /if %>
<% if ($foto.img_descshort!='') %> <br> <% $foto.img_descshort%> <% /if %>

<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.edit==TRUE)) %>
        <br><a title="bearbeiten" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=edit">
        <img alt="bearbeiten" title="bearbeiten" src="<%$SSL_PATH_SHOP%><%$PATH_SHOP%>images/opt_edit.png" ></a>
<%/if%>        
<% if ($customer.kid>0 && ($customer.kid==$foto.imginfo.pic_kid || $customer.PERM.del==TRUE)) %>
        <a title="löschen" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$gallery_obj.gid%>&start=<%$paging.start%>&picid=<%$foto.imginfo.PICID%>&aktion=delpic">
        <img alt="löschen" title="löschen" src="<%$SSL_PATH_SHOP%><%$PATH_SHOP%>images/opt_del.png" ></a> 
<%/if%>                
 </td>
        <% if ($smarty.foreach.gloop.iteration % $line_break == 0)%></tr><tr><%/if%>
<% /foreach %>
</tr>
</table>
<% /if %>