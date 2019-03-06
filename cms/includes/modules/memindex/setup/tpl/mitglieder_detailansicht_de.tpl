<table class="tab_std" width="100%" >
 <tr>
<% if ($member.img!="") %> <td width="<% $gbl_config.opt_box_thumb_width %>">
<img src="<% $member.img %>" ></td><% /if %>
 <td valign="top"><h1><% $member.anrede %> <% $member.vorname %> <% $member.nachname %></h1>

<% if ($member.firma!="") %>
<% $member.firma %><br>
<% else %>
<% $member.vorname %>, <% $member.nachname%><br>
<% /if %>
<% $member.strasse %><br>
<% $member.plz%> <% $member.ort%><br><br>
<% if ($member.tel !="") %> Telefon: <% $member.tel %><br><% /if %>
<% if ($member.fax !="") %> Fax: <% $member.fax %><br><% /if %>

<br>
<% if ($member.homepage!="") %> Internet: <a href="<% $member.homepage %>" target="_blank"><% $member.homepage %></a><br><% /if %>
<% if ($member.email !="") %> Email: <% $member.email %><br><% /if %>

<% if ($member.collectiontogroup) %>
<% foreach from=$member.collectiontogroup item=colg name=cgloop %>
 <h6><% $colg.collection.col_name %></h6>
 <ul style="list-style:none">
 <% foreach from=$colg.groups item=group name=gloop %>
  <li><% $group.groupname %></li>
<% /foreach %>
</ul>
<% /foreach %>
<% /if %>

</td></tr></table>
