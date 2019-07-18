<%include file="cb.panel.header.tpl" icon="fa-files-o" title="Used CSS Files"%>
<% if count($LAY.used_css_files)>0%>
<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="save_css_order">
    	<table class="table table-striped table-hover">
        <thead>
        <tr>
        	<th>CSS File</th>
        	<th>Sort.</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
    <% foreach from=$LAY.used_css_files item=row %>
    <tr>
       <td><a href="javascript:void(0);" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=show_css_edit&file=<%$row.l_file|escape:"url"%>', 'Editor')"><%$row.l_file%></a></td>
       <td><input type="text" class="form-control" size="3" value="<%$row.l_order%>" name="FORM[<%$row.id%>][l_order]"><input type="hidden" value="<%$row.id%>" name="FORM[<%$row.id%>][id]"></td>
       <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></div></td>
    </tr>
    <%/foreach%>
    </tbody>
</table>
    <%$subbtn%>
</form>

<div class="alert alert-info mt-3">
Diese CSS Dateien werden in 1 Datei zusammengeführt und komprimiert: <a href="../file_data/template/css/template.css" target="_css">/file_data/template/css/template.css</a>
</div>

<script>set_ajaxdelete_icons('{LBL_CONFIRM}', '<%$epage%>');init_autojson_submit();set_ajaxapprove_icons();</script>
<%else%>
    <p class="alert alert-info">Keine CSS Dateien gefunden.</p>
<%/if%>
<%include file="cb.panel.footer.tpl"%>
