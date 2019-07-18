<%include file="cb.page.title.tpl" icon="fas fa-home" title="Themen Manager"%>

<% if ($cmd=='show_all' || $cmd=='' || $cmd=='ax_show_all') %>
    <% include file="toplevel.table.tpl"%>
<%/if%>

<% if ($cmd=='edit' || $cmd=='ax_topl_edit') %>
        <% include file="toplevel.edit.tpl"%>
<%/if%>