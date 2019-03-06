<fieldset class="plugin">
<label>eKomi Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>

<label>Anzahl:</label>
    <input size="4" maxlength="4" type="text" class="form-control" name="PLUGFORM[limit]" value="<% $WEBSITE.node.tm_plugform.limit|sthsc %>">
</fieldset>   