<style type="text/css">   @import url(<% $PATH_CMS %>js/images/lytebox/lytebox.css);</style>
<script type="text/javascript" src="<% $PATH_CMS %>js/images/lytebox/lytebox.js"></script>

<table class="tab_std" width="100%">
<% foreach from=$event_latest_items item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<tr class="<% $sclass %>" >
    <td><% $mdate.date %></td>
    <td><a title="<% $mdate.title %>" href="<% $mdate.detail_link_rel %>"><strong><% $mdate.title %></strong></a><br>
    <% $mdate.introduction %></td>
    <td><% $mdate.time_from %> - <% $mdate.time_to %></td>
    <td><% $mdate.place %></td>
    <td align="right">
        <a target="_new" rev="width: 800px; height: 300px; scrolling: yes;" title="<% $mdate.img_title %>" rel="lyteframe[<% $mdate.groupname %>]" href="<% $mdate.detail_link_popup_rel %>">
            <img src="<% $PATH_CMS %>js/images/lytebox/lupe.gif" >
        </a>
    </td>
  
</tr>
<%/foreach%>
</table>
