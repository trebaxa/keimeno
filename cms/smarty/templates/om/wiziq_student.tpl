<% if ($customer.wiziq_isteacher==true) %>
    <% include file="wiziq_appointments_teacher.tpl" %>
<%else%>

    
    <% include file="wiziq_calendar.tpl" %>
   
<%/if%>
