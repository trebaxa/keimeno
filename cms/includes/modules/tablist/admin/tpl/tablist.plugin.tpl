<div class="form-group">
    <label>Titel:</label>
    <input class="form-control" type="text" name="PLUGFORM[title]" value="<%$WEBSITE.node.tm_plugform.title%>" required />
</div>
<div class="form-group">
    <label>Tabellen Auswahl:</label>
    <select class="form-control" name="PLUGFORM[tabid]">
     <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
        <option <% if ($WEBSITE.node.tm_plugform.tabid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
     <%/foreach%>
    </select>
</div>