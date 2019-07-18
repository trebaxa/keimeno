<div class="btn-group">
    <a class="ajax-link btn btn-secondary" data-target="js-cust-editor" href="<%$eurl%>cmd=edit_cust&kid=<%$GET.kid%>">Kunden Daten</a>
    <a class="ajax-link btn btn-secondary" data-target="js-cust-editor" href="<%$eurl%>cmd=show_docs&kid=<%$GET.kid%>">Dokumente</a>
</div>

<div id="js-cust-editor">     
        <%include file="memindex.editor.data.tpl"%>
</div>        