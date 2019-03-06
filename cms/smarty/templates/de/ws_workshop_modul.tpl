<% if ($cmd=="load_workshop") %>
  <% include file="ws_workshop_detail.tpl" %>
<%/if%>

<% if ($cmd=="load_workshops") %>
  <% include file="ws_workshop_auflistung.tpl" %>
<%/if%>

<% if ($cmd=="") %>
  <% include file="ws_staedte_auflistung.tpl" %>
<%/if%>

<% if ($cmd=='book_now') %>
  <% include file="ws_register.tpl" %>
<%/if%>

<% if ($cmd=='load_paypal') %>
  <% include file="ws_paypal.tpl" %>
<%/if%>