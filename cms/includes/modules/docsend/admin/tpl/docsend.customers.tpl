<% if count($DOCSEND.customers)>0%>
    <% include file="paging.admin.tpl" %>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>                
                <th></th>
            </tr>
        </thead>
        <% foreach from=$DOCSEND.customers item=row %>
            <tr>                
                <td><% $row.vorname %> <% $row.nachname %></td>                
                <td class="text-right"><div class="btn-group">
                    <a href="javascript:void(0);" onclick="$('#js-customer-kid').val('<%$row.kid%>');$('#ls-target').html('');$('#js-customer').html('<%$row.kid%>, <%$row.vorname%> <%$row.nachname%>');ds_check();$('#js-ls-ds').val('')" class="btn btn-warning btn-sm"><i class="fa fa-arrow-right"></i></a>
                </td>
            </tr>
        <%/foreach%>
    </table>
    
<%/if%>