<h1>Neueste Mitglieder</h1>
<% if ($lastcustomers) %>
<table  width="100%">
<% foreach from=$lastcustomers item=member name=memgbloop %>
<tr>
<% if ($member.img!="") %> 
<td width="<% $gbl_config.opt_box_thumb_width %>"><img src="<% $member.img %>" ></td>
<% /if %>
<td valign="top" align="center">
<h1>
<% $member.anrede %> <% $member.vorname %> <% $member.nachname %></h1>
<br><strong>Anschrift</strong><br>
<% if ($member.firma!="") %>
<% $member.firma %><br>
<% else %>
<% $member.vorname %>, <% $member.nachname%><br>
<% /if %>
<% $member.strasse %><br>
<% $member.plz%> <% $member.ort%><br><br>
<% if ($member.tel_office !="") %> Telefon: <% $member.tel_office %><br><% /if %>
<% if ($member.fax !="") %> Fax: <% $member.fax %><br><% /if %>
<br>
<a title="<% $member.vorname%> <% $member.nachname %>" href="<% $member.link %>">Profil ansehen</a>
</td>
<tr><td colspan="2"><div style="height:3px;float:left;border-bottom:3px;border-top:3px dashed #A40000;width:100%"></div></td></tr>
</tr>
<% /foreach %>
</tr>
</table>
<% /if %>
