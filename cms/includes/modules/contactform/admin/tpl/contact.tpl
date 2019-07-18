<%include file="cb.page.title.tpl" title="Kontakt Formular"%>


<% if (count($CONTACT.items)>0) %>
<%include file="cb.panel.header.tpl" title="Anfragen"%>
<table class="table table-striped table-hover">
    <thead><tr>
        <th>Datum</th>
        <th>Betreff</th>
        <th>Text</th>
        <th>Empfänger</th>
        <th>Sender</th>
        <th>CC Kopie</th>
        <th>Disclaimer</th>
        <th></th>
    </tr></thead>
    <tbody>
<% foreach from=$CONTACT.items item=row %>
    <tr>
        <td><%$row.date %></td>
        <td><%$row.c_subject %></td>
        <td><%$row.c_text|nl2br %></td>
        <td><%$row.c_recipient  %></td>
        <td><%$row.c_sender   %></td>
        <td><%$row.c_cc    %></td>
        <td><a href="#" title="am <%$row.date %> <% $row.c_disclaimer_sign.ip%>"><i class="fa fa-check"></i></a></td>
        <td><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></td>
    </tr>
<%/foreach%>
</tbody>
</table>
<%include file="cb.panel.footer.tpl"%>
<%/if%>


        <%$CONTACT.conf%>
        