<div class="page-header">
    <h1><i class="fa fa-home"><!----></i>Themen Manager</h1>
</div>

<% if ($cmd=='show_all' || $cmd=='' || $cmd=='ax_show_all') %>
    <% include file="toplevel.table.tpl"%>
<%/if%>

<% if ($cmd=='edit' || $cmd=='ax_topl_edit') %>
        <% include file="toplevel.edit.tpl"%>
<%/if%>