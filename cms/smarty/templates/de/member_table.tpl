<% if ($memberlist) %>
<br>
<% assign var=line_break value="1" %>
<table class="tab_std" border="1" width="100%">
<tr class="header">
    <td>Anrede</td>
    <td>Nachname</td>
    <td>Vorname</td>
    <td>PLZ</td>
    <td>Ort</td>    
   </tr>

<% foreach from=$memberlist item=member name=membloop %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<tr class="<% $sclass %>">
 <td valign="middle" align="left"><% $member.anrede %></td>
 <td valign="middle" align="left"><a title="anzeigen" href="<% $member.link %>"><% $member.nachname %></a></td>
 <td valign="middle" align="left"><% $member.vorname %></td>
 <td valign="middle" align="left"><% $member.plz %></td>
 <td valign="middle" align="left"><% $member.ort %></td>
 </tr>
<% /foreach %>
</tr>
</table>
<% /if %>

<% if ($memberlistcount==0 && $global_err=="") %>
<h4> Kein gefunden</h4>
<% /if %>