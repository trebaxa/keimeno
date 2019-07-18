 <%include file="cb.page.title.tpl" icon="far fa-images" title="CMS default images"%>

    

    <% foreach from=$LAY.images item=row %>
        <h3><%$row.title%></h3>
        <form class="layfileup" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
    
            <table class="table table-striped table-hover">
                <tr>
                    <td><%$row.label%>:</td>
                    <td>
                        <input type="file"  name="datei" size="30" class="file_btn autosubmit" value="durchsuchen">
                        <% if ($row.err!="") %><br><span class="redspan"><%$row.err%></span><%/if%>
                        <% if ($row.msg!="") %><br><span class="greenspan"><%$row.msg%></span><%/if%>
                    </td>
                    <td class="text-right">
                        <% if ($row.exists==true) %>
                            <a href="<%$row.target%>" target="_blank"><img class="img-thumbnail" src="<%$row.thumb%>"  alt="<%$row.label|sthsc%>"></a><br>
                            <%$row.width%>x<%$row.height%>
                        <%/if%>
                    </td>
                    <td class="text-right">
                        <input onclick="toggle_off();" id="img-<%$row.label|md5%>" alt="" type="submit" class="btn btn-primary pull-right" value="upload">
                        <% if ($row.exists==true) %><button onclick="return delete_fup('<%$row.ident%>')" class="btn btn-danger redbutton pullright" style=""><i class="fa fa-trash"><!----></i></button><%/if%>
                    </td>
                    
                </tr>
            </table>
    
            <input type="hidden" name="cmd" value="fileupload">
            <input type="hidden" name="ftarget" value="<%$row.target%>">
            <input type="hidden" name="ident" value="<%$row.ident%>">
            <input type="hidden" name="epage" value="<%$epage%>">
        </form>
    <%/foreach%>


<script>
    $('.layfileup').submit(function() {
        $(this).ajaxSubmit(fuoptions);
        return false;
    });
</script> 