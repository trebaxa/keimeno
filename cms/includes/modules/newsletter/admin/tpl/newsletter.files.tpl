 <% if (count($NEWSLETTER.attachments)>0) %>
 <div class="form-group">
    <label>Attachments:</label>
    <table class="table">
        <% foreach from=$NEWSLETTER.attachments item=row %>	
            <tr><td><a target="_blank" href="<%$row.relativefile%>"><%$row.bafile%> <%$row.fs%></a></td><td class="text-right"><%$row.delicon%></td></tr>
        <%/foreach%>
    </table>
 </div>
<%/if%>   