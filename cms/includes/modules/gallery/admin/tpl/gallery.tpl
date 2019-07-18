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
   <%include file="cb.page.title.tpl" icon="far fa-image" title="Konfiguration"%>
   <%$GALADMIN.conf%>
<%/if%>