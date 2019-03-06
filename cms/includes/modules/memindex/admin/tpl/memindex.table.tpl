<% if count($CUST.table)>0%>
    <% include file="paging.admin.tpl" %>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><a href="kreg.php?start=<%$GET.start%>&col=kid&dc=<% $paging.flipped_dc %>">#</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=nachname&dc=<% $paging.flipped_dc %>">{LA_NACHNAME}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=vorname&dc=<% $paging.flipped_dc %>">{LA_VORNAME}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=email&dc=<% $paging.flipped_dc %>">Email</a></th>                
                <th>{LA_TELEFON}</th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=firma&dc=<% $paging.flipped_dc %>">{LA_FIRMA}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=plz&dc=<% $paging.flipped_dc %>">{LA_PLZ}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=ort&dc=<% $paging.flipped_dc %>">{LA_ORT}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=L.land&dc=<% $paging.flipped_dc %>">{LA_LAND}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=datum&dc=<% $paging.flipped_dc %>">{LA_EINGETRAGENAM}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=mailactive&dc=<% $paging.flipped_dc %>">{LA_NEWSLETTER}</a></th>
                <th><a href="kreg.php?start=<%$GET.start%>&col=sperren&dc=<% $paging.flipped_dc %>">{LA_GESPERRT}</a></th>
                <th></th>
            </tr>
        </thead>
        <% foreach from=$CUST.table item=row %>
            <tr>
                <td><a href="kreg.php?cmd=show_edit&kid=<% $row.kid %>"><% $row.kid %></a></td>
                <td><a href="kreg.php?cmd=show_edit&kid=<% $row.kid %>"><% $row.nachname %></a></td>
                <td><a href="kreg.php?cmd=show_edit&kid=<% $row.kid %>"><% $row.vorname %></a></td>
                <td><% mailto address=$row.email encode="hex" %><small><% $row.email_notpublic %></small></td>                
                <td><% $row.tel %></td>
                <td><% $row.firma %></td>
                <td><% $row.plz %></td>
                <td><% $row.ort %></td>
                <td><% $row.COUNTRYNAME %></td>
                <td><% $row.datum_ger %></td>
                <td><% if ($row.mailactive==1) %>{LA_AAKTIV}<%else%>-<%/if%></td>
                <td><% if ($row.sperren==1) %>{LBL_YES}<%else%>-<%/if%></td>
                <td class="text-right"><div class="btn-group">
                <a href="kreg.php?cmd=show_edit&kid=<% $row.kid %>" class="btn btn-default"><i class="fa fa-pencil"></i></a>
                <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></div></td>
            </tr>
        <%/foreach%>
    </table>
    <% include file="paging.admin.tpl" %>
<%/if%>
<script>set_ajaxdelete_icons('{LBL_CONFIRM}', '<%$epage%>')</script>