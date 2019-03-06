<% include file="newseditor.tpl"%>

<% if ($aktion=="show") %> 
    <% include file="newsdetail.tpl"%>
<% /if %>

<% if ($aktion=="") %>
    <% include file="newslist.tpl" %>
<% /if %>