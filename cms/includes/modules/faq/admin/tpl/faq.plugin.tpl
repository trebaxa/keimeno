<fieldset class="plugin">
<label>FAQ Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
<label>Gruppen Filter</label>
    <select class="form-control" name="PLUGFORM[groupid]">
    <option <% if ($WEBSITE.node.tm_plugform.groupid==0) %>selected<%/if%> value="0">- kein Filter -</option>
        <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.groupid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>    
     
</fieldset>   