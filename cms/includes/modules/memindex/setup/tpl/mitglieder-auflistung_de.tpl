<% include file="member_groups.tpl" %>
<hr>
<% include file="member_alpha.tpl" %>
<hr>
<% include file="member_alpha2.tpl" %>
<hr>
<% include file="member_detailsearch.tpl" %>
<hr>

<% if ($aktion=="showcustomer") %>
 <% include file="member_detail.tpl" %>
<% /if %>

<% if ($aktion=="showcol") %>
<% include file="member_collectionlist.tpl" %>
<% /if %>

<% if ($aktion=='showgroup') %>
 <% include file="member_collectiongroups.tpl" %>
 <!--
 <h1><% $member_group.groupname %></h1>
 <% include file="member_table.tpl" %>
-->
<% /if  %>

<% if ($aktion=='showalpha' || $aktion=='detailsearch' || $aktion=='showall' || $aktion=='search') %>
 <h1><% $member_group.groupname %></h1>
 <% include file="member_table.tpl" %>
<% /if  %>
