<h3>Einträge</h3>

<div class="btn-group"><a class="btn btn-default" href="javascript:void(0);" onclick="$('#faqform').trigger('reset');$('#additem .id').val('');$('#additem').modal('show');init_faqs_tiny();">Eintrag anlegen</a></div>


<div id="faqedititem"></div>

<% if (count($FAQ.faqitems)>0) %>
 <form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="save_items">
<table class="table table-striped table-hover">
<thead>
<tr>
    <th>Frage</th>
    <th>Anwort</th>
    <th>Sort.</th>
    <th></th>
</tr>
</thead>
<% foreach from=$FAQ.faqitems item=row %>
    <tr>
        <td><%$row.faq_question%><input type="hidden" name="FORM[<%$row.id%>][id]" value="<%$row.id%>"></td>
        <td><%$row.faq_answer|st|truncate:100%></td>
          <td><input type="text" class="form-control" size="3" maxlength="3" name="FORM[<%$row.id%>][faq_order]" value="<%$row.faq_order|sthsc%>"></td>
        <td><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
        <a class="btn btn-default" href="javascript:void(0)" onclick="edit_item(<%$row.id%>)"><span class="glyphicon glyphicon-pencil"><!----></span></a></td>
    </tr>
<%/foreach%>
</table>
<%$subbtn%>
</form>
<%else%>
Keine Einträge vorhanden.
<%/if%>

<script>
init_autojson_submit();
function reload_faq_items() {
    simple_load('faqitems','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_items&gid=<%$GET.gid%>');
}

function edit_item(itemid) {
    var url = '<%$PHPSELF%>?epage=<%$epage%>&cmd=getitem&id='+itemid;
    load_json_form(url, 'faqform');
    $('#additem').modal('show');
    init_faqs_tiny();
}

</script>
<% if ($GET.axcall==1) %><script>set_ajaxdelete_icons('{LBL_CONFIRM}', '<%$epage%>');</script><%/if%>