<div class="page-header"><h1>Videothek</h1></div>

<% if ($section=='vimeosync') %>
 <% include file="video.vimeo.tpl" %>
<%/if%>

<% if ($section=='modstylefiles') %>
 <% include file="modstylefiles.tpl"%>
<%/if%>

<% if ($section=='search') %>
 <% include file="video.search.tpl"%>
<%/if%>

<% if ($section=='cats') %>
 <% include file="video.cats.tpl"%>
<%/if%>

<% if ($section=='showresult') %>
 <% include file="video.results.tpl"%>
<%/if%>

<% if ($section=='videomanager' || $section=='start') %>
 <% include file="video.manager.tpl"%>
<%/if%>

<% if ($section=='conf') %>
 <% $VIM.CONFTAB %>
<%/if%>


