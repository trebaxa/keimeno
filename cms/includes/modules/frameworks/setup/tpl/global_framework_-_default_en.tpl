<% include file="fw_header.tpl" %>

<!-- LEFT COLUMN -->
<div id="left_nav">
<% if ($customer.kid>0) %>
 {LBL_WELCOME} <% $customer.vorname %> <% $customer.nachname %>
<% /if %>
<%include file=$globl_tree_template %>
{TMPL_SPOT_2}
</div>

<!-- CONTENT --> 
<div id="middle_content"> 

<div id="ax_main"></div>
<div id="bread"><div class="breadlabel">Sie sind hier:</div>
<ul><% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread %>
<li> &#x9B; <a title="<%$bread.linkname|hsc%>" href="<%$bread.link%>"><%$bread.label%></a></li>
<%/foreach%></ul></div>
<div class="clearer"></div>
<% include file="feedback_messages.tpl" %>
{TMPL_SPOT_1}
</div>

<% include file="fw_footer.tpl" %>
