<link href='../includes/modules/event/admin/js/fullcalendar-1.6.4/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='../includes/modules/event/admin/js/fullcalendar-1.6.4/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../includes/modules/event/admin/js/fullcalendar-1.6.4/fullcalendar/fullcalendar.min.js'></script>


<div class="page-header"><h1>Kalender <% $EVENT.caltheme.groupname %></h1></div>
 <style type="text/css">
   @import url(../js/images/event/calendar.css);
</style>

<% if ($cmd=='edit') %>
	<% include file="calendar.editor.admin.tpl" %>
<% /if %>

<% if ($cmd=='load_events') %>
   <% include file="calendar.events.tpl" %>
<%/if%>

<% if ($cmd=='conf') %>
   <% $EVENT.conf %>
<%/if%>

<% if ($cmd=='calgroups') %>
 <% include file="calendar.groups.tpl" %>
<%/if%>

<% if ($cmd=='edit_group') %>
   <% include file="calendar.group.editor.tpl" %>
<%/if%>