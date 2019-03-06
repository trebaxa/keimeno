<ul class="nav nav-tabs bar_tabs">
 <li class="active"><a class="ajax-link" data-target="js-cust-editor" href="<%$eurl%>cmd=edit_cust&kid=<%$GET.kid%>">Kunden Daten</a></li>
 <li><a class="ajax-link" data-target="js-cust-editor" href="<%$eurl%>cmd=show_docs&kid=<%$GET.kid%>">Dokumente</a></li>
</ul>

<div id="js-cust-editor">     
        <%include file="memindex.editor.data.tpl"%>
</div>        