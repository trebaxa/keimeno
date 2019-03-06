<% include file="fw_header.tpl" %>
<!-- CONTENT --> 
<div id="ax_main"></div>
<ol class="breadcrumb">
    <li>Sie sind hier:</li>
    <% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread %>
        <li><a title="<%$bread.linkname|hsc%>" href="<%$bread.link%>"><%$bread.label%></a></li>
    <%/foreach%>
</ol>
<% include file="feedback_messages.tpl" %>
{TMPL_SPOT_1}
<% include file="fw_footer.tpl" %>