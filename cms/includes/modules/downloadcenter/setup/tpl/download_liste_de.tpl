<h1>Dokumente</h1>

<% if ($downloads) %>
<br>

<table class="tab_std"  width="100%">

<% foreach from=$downloads item=fileobj name=membloop %>
<% if ($fileobj.fileexists) %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %><% else %> <% assign var=sclass value="row1" %><% /if %>
<tr class="<%$sclass%>">
 <td valign="middle" align="left"><% $fileobj.title %></td>
<td valign="middle" align="center"><a target="_blank" title="Download <% $fileobj.title %>"  href="<% $fileobj.link %>"><img title="Download <% $fileobj.title %>" alt="Download <% $fileobj.title %>"  src="<% $PATH_CMS %>images/opt_download.gif"></a><br>
<% $fileobj.filesize %>
</td>
</tr>
<% /if %>
<tr colspan="2" > <td valign="middle" align="left"><% $fileobj.description %></td></tr>
<% /foreach %>
</table>
<%else%>
<div class="infobox">Es liegen derzeit keine Dokumente vor.</div>
<% /if %>
