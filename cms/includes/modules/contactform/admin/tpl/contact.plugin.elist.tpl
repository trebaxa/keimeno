<% if (count($CONTACT.elist)>0 ) %>
<table class="table table-bordered table-striped table-hover mt-lg">
    <thead>
        <tr>
            <th>Label</th>
            <th>Email</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <%foreach from=$CONTACT.elist item=row %>
        <tr>
            <td><%$row.label%></td>
            <td><%$row.email%></td>
            <%include file="cb.icons.tpl"%>
        </tr>
    <%/foreach%>
    </tbody>
</table>
<%/if%>