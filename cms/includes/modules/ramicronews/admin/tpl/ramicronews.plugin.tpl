<fieldset class="plugin">
    <label>RA Micro News Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
    
    <div class="form-group">
        <label>Anzahl</label>
        <input type="text" class="form-control" value="<%$WEBSITE.node.tm_plugform.limit|sthsc%>" name="PLUGFORM[limit]" />
    </div>
     
</fieldset>   