<table id="langtabled" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>{TEMPLATE}</th>
            
            <% foreach from=$LANGEDIT.header item=row %>
                <th><img src="<%$row.flag%>"><b><%$row.post_lang%></b><br><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=download&id=<%$row.id%>&admin=<%$REQUEST.admin%>">Download</a></th>
            <%/foreach%>
            
            <th></th>
        </tr>
    </thead>
    
    <% foreach from=$LANGEDIT.lines item=row %>
        <tr>
            <td>{<%$row.keywordhex%>}</td>            
            <% foreach from=$row.trans item=trans %>
                <td>
                  <% if ($trans.valkey=="") %>
                    <span class="clickedit" data-formname="FORM[<%$trans.valwert%>][<%$trans.keyword%>]">- {LA_TRANSLATE} -</span>
                 <%else%>
                    <code class="clickedit" data-formname="FORM[<%$trans.valwert%>][<%$trans.keyword%>]"><%$trans.valkey|stripslashes|hsc%></code>
                 <%/if%>
                </td>
            <%/foreach%>
            
            <td><%$row.delicon%></td>
        </tr>
    <%/foreach%>
</table>

<script>
$('.clickedit').css('cursor','pointer');
$('.clickedit').unbind('click');
$('.clickedit').click(function(event) {
    event.preventDefault();
    $('#editinputfield').remove();
    $('.clickedit').show();
    if ($(this).html().length>31) {
        $(this).after('<textarea class="form-control" id="editinputfield" name="'+$(this).data('formname')+'">'+$(this).html()+'</textarea>');
    } else {
        $(this).after('<input class="form-control" autocomplete="off" id="editinputfield" type="text" value="'+$(this).html()+'" name="'+$(this).data('formname')+'">');
    }    
    $(this).hide();
    $('#editinputfield').focus();
    var spanfield =$(this);
    $('#editinputfield').blur(function() {        
        spanfield.after('<img src="./images/axloader.gif" id="editfieldloader" style="width:16px">');
        spanfield.html(escapeHtml($(this).val()));        
        execrequest('<%$PHPSELF%>?admin=<%$GET.admin%>&epage=<%$epage%>&cmd=updatelang&'+$(this).attr('name')+'='+$(this).val());
        $('#editinputfield').remove();
        $('.clickedit').show();
        window.setTimeout("$('#editfieldloader').remove()",500);
    });
    $('#editinputfield').keypress(function(e) {
        if(e.which == 13) {
            $( "#editinputfield" ).trigger( "blur" );
        }
    });    
});

</script>    


<% if ($GET.axcall==1) %><script>set_ajaxdelete_icons('{LBL_CONFIRM}', '<%$epage%>');</script><%/if%>

<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="langtabled" scope="global"%>
<%include file="table.sorting.script.tpl"%>