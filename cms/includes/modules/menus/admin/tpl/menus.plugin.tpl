<div class="form-group">
    <label>Template:</label>
    <select class="form-control" name="PLUGFORM[menuid]">
        <% foreach from=$WEBSITE.PLUGIN.result.menus item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.menuid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
    
</div>    