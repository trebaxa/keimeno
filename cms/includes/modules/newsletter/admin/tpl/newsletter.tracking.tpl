<% if (count($NEWSLETTER.all_feedback )>0) %> 
<h3>{LBLA_NEWSHASREAD} (<%$NEWSLETTER.all_feedback_count%>):</h3>
        <table class="table table-striped table-hover" id="okfeedback-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>{LBLA_READED} X</th>
                    <th>Status</th>
                </tr>
           </thead>     
           <tbody>   
        <% foreach from=$NEWSLETTER.all_feedback key=email item=row %>
            <tr>
                <td><%$row.email %></td>
                <td><%$row.readed %></td>
                <td>
                <% if ($row.readed==0) %>
                    <i class="fa fa-warning fa-red"><!----></i>
                <%else%>
                    <i class="fa fa-check-circle fa-green"><!----></i>                    
                <%/if%>    
                </td>
            </tr>
        <%/foreach%>
        </tbody>
    </table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="okfeedback-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>       
<%/if%> 