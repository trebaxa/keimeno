<% if ($section=='start') %>
    <%include file="gallery.picmananger.tpl"%>
<%/if%>

<% if ($section=='tools') %>
    <%include file="gallery.tools.admin.tpl"%>
<%/if%>

<% if ($section=='edit') %>
    <%include file="gallery.editor.tpl"%>
<%/if%>

<% if ($section=='conf') %>
   <div class="page-header"><h1><i class="fa fa-photo"><!----></i>Konfiguration</h1></div><%$GALADMIN.conf%>
<%/if%>