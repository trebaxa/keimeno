<%*$MEMINDEX.myfiles|echoarr*%>
<% if (count($MEMINDEX.myfiles)>0) %>
  <table class="table table-hover table-striped">
    <tbody>
  <% foreach from=$MEMINDEX.myfiles item=row %>
    <tr>
        <td><a href="<%$PHPSELF%>?page=<%$page%>&cmd=user_file_download&hash=<%$row.hash%>&folder=<%$row.file_to_root_hash%>"><%$row.file%></a></td>
        <td><%$row.size%>Bytes</td>
        <td><%$row.date%></td>
        <td class="text-right"><a href="<%$PHPSELF%>?page=<%$page%>&cmd=user_file_download&hash=<%$row.hash%>&folder=<%$row.file_to_root_hash%>" class="btn btn-default"><i class="fa fa-download"></i></a></td>
    </tr>
  <%/foreach%>
  </tbody>
  </table>    
<%/if%>