<b><%$MEMINDEX.folder%></b>
<% if (count($MEMINDEX.files)>0) %>
<table class="table table-hover table-striped">
     <tbody>
    <% foreach from=$MEMINDEX.files item=row %>
        <tr>
            <td><%$row.file%></td>
            <td><%$row.size%>Bytes</td>
            <td><%$row.date%></td>
            <td><% if ($row.last_download_ger!="") %><span title="Download Datum"><%$row.last_download_ger%></span><%/if%></td>
            <td><a href="<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>" class="btn btn-secondary"><i class="fa fa-download"></i></a></td>        
            <td><%include file="cb.icons.tpl"%></td>
        </tr>
    <%/foreach%>
    </tbody>
</table>
<a class="btn btn-secondary json-link" href="<%$eurl%>cmd=senf_fileinfo_email&kid=<%$GET.kid%>">Kunden per E-Mail informieren</a>
<%/if%>

