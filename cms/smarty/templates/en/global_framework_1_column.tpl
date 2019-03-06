<% include file="fw_header.tpl" %>

<!-- CONTENT --> 
<div id="middle_content"> <% include file="banner_rotation.tpl" %>
<% if ($global_err) %>
<div class="faultbox">
<% foreach from=$global_err item=error name=errloop %>               
<span class="important"><% $error %></span>
<% /foreach %>
</div>
<% /if %>

<div id="ax_main"></div>
<div id="bread"><ul><% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread %><li><%$bread%></li><%/foreach%></ul></div>
<% include file="feedback_messages.tpl" %>

{TMPL_SPOT_1}

<% include file="social_network.tpl" %>
</div>{TMPL_SPOT_2}



<% include file="fw_footer.tpl" %>