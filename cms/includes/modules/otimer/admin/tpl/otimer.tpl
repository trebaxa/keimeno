<script src='../includes/modules/otimer/admin/js/moment.js'></script>
<link href='../includes/modules/otimer/admin/js/fullcalendar-3.1.0/fullcalendar.css' rel='stylesheet' />
<link href='../includes/modules/otimer/admin/js/fullcalendar-3.1.0/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../includes/modules/otimer/admin/js/fullcalendar-3.1.0/fullcalendar.min.js'></script>


<div class="page-header"><h1>Reservierungen</h1></div>

<% if ($cmd=='') %>
    <%include file="otimer.events.tpl"%>
<%/if%>
    
<% if ($cmd=='blacklist') %>
    <%include file="otimer.blacklist.tpl"%>
<%/if%>

<% if ($cmd=='worktime' || $cmd=='worktimem') %>
    <%include file="otimer.wtt.tpl"%>
<%/if%>

<% if ($cmd=='overview') %>
    <%include file="otimer.overview.tpl"%>
<%/if%>

<% if ($cmd=='otprograms') %>
    <%include file="otimer.otprograms.tpl"%>
<%/if%>

<% if ($cmd=='otgroups') %>
    <%include file="otimer.otgroups.tpl"%>
<%/if%>    

<% if ($cmd=='dayoptions') %>
    <%include file="otimer.dayopt.tpl"%>
<%/if%>  