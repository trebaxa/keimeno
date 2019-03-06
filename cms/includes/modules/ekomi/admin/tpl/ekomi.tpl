<div class="page-header"><h1>eKomi</h1></div>

<% if ($section=='start')%>
    <% $CONFIG.config%>
    <h3>Letzte Bewertungen</h3>
    <table class="table table-striped table-hover" width="40%" id="ekomi-rating-table">
        <thead><tr>
            <th>Datum</th>
            <th>Order ID</th>
            <th>Stars</th>
            <th>Reviews</th>
        </tr></thead>
        <% foreach from=$ekomi.REVIEWS item=review  %>
            <tr>
                    <td><% $review.date%></td>  
                    <td><% $review.customer%></td>
                    <td width="160"><%section name=foo start=0 loop=$review.stars step=1%><i class="fa fa-star fa-green">&nbsp;</i><%/section%></td>
                    <td><% $review.review%></td>
            </tr>
        <% /foreach %>
    </table>
    <%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="ekomi-rating-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>   
<%/if%>

<% if ($section=='ekomi_emails')%>
    <%include file="ekomi.mails.tpl"%>
<%/if%>

<% if ($section=='conf')%>
<h3> eKomi  - Config</h3>
        <%$ekomi.conf%>
<%/if%>

