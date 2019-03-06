<% if (count($err_msgs)>0) %>
<div class="bg-danger text-danger">
<%foreach from=$err_msgs item=msge %>
 <%$msge%><br>
<%/foreach%>
</div>
<%/if%>

<% if (count($ok_msgs)>0) %>
<div class="bg-success text-success">
<%foreach from=$ok_msgs item=msg %>
 <%$msg%><br>
<%/foreach%>
</div>
<%/if%>