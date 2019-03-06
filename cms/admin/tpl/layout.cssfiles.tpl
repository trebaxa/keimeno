<%include file="cb.panel.header.tpl" icon="fa-files-o" title="found on server"%>
<% if count($LAY.css_files)>0%>
	<table class="table table-striped table-hover">
        <thead>
            <tr>
    	       <th>CSS File</th>
    	       <th></th>
            </tr>
        </thead>
    <tbody>
        <% foreach from=$LAY.css_files item=row %>
        <tr>
           <td><a href="javascript:void(0);" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=show_css_edit&file=<%$row.file|escape:"url"%>', 'Editor')"><%$row.file%></a>
           </td>
           <td class="text-right"><i class="fa fa-arrow-right js-cssadd" data-file="<%$row.file|escape:"url"%>"></i></td>    
        </tr>
        <%/foreach%>
    </tbody>
</table>
<%else%>
    <p class="alert alert-info">Keine CSS Dateien gefunden.</p>
<%/if%>   
<%include file="cb.panel.footer.tpl"%>

<script>
$( ".js-cssadd" ).click(function() {
    simple_load('usedcss','<%$PHPSELF%>?epage=<%$epage%>&cmd=add_cssfile&cssfile='+$(this).data('file'));
});

function load_used_css() {
     simple_load('usedcss','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_usedcss');
}

</script> 