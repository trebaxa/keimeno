<div class="box">
<h1>Neueste Artikel</h1>
<table class="tab_std" width="100%">
<% foreach from=$articles_last_items item=looparticle %>     
<tr><td><a href="<%$looparticle.link%>" title="<%$looparticle.a_title%>">
<img src="<%$looparticle.thumbnail%>" ></a>
</td>
<td>
<a href="<%$looparticle.link%>" title="<%$looparticle.a_title%>"><strong><%$looparticle.ac_title%></strong></a>
<br><span class="small"><%$looparticle.date%> Autor:<%$looparticle.a_author%></span>
<% if ($looparticle.AFCOUNT>0) %><img alt="Attachment" title="<%$looparticle.AFCOUNT%> Anh&auml;nge" src="<%$PATH_CMS%>images/attach.png" ><%/if%>
</td></tr>
<% /foreach %>
</table>
</div>
