<fieldset class="plugin">
<div class="form-group">
    <label>Facebook Gruppe Auswahl:</label>
    <select class="form-control custom-select" name="PLUGFORM[fbwpid]">
     <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
        <option <% if ($WEBSITE.node.tm_plugform.fbwpid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
     <%/foreach%>
    </select>
</div>
<div class="form-group">
    <label>Facebook Group Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
   </div> 
    <div class="form-group">
        <label>Anzahl</label>
        <input type="text" class="form-control" value="<%$WEBSITE.node.tm_plugform.limit|sthsc%>" name="PLUGFORM[limit]" />
    </div>
     
</fieldset>   